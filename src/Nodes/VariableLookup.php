<?php

namespace Keepsuit\Liquid\Nodes;

use Keepsuit\Liquid\Contracts\CanBeEvaluated;
use Keepsuit\Liquid\Contracts\HasParseTreeVisitorChildren;
use Keepsuit\Liquid\Contracts\IsContextAware;
use Keepsuit\Liquid\Exceptions\SyntaxException;
use Keepsuit\Liquid\Render\RenderContext;
use Keepsuit\Liquid\Support\MissingValue;
use Keepsuit\Liquid\Support\UndefinedVariable;

class VariableLookup implements CanBeEvaluated, HasParseTreeVisitorChildren
{
    const FILTER_METHODS = ['size', 'first', 'last'];

    private const LOOKUP_WORD = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789_-';

    /**
     * @var int[]
     */
    public readonly array $lookupFilters;

    public function __construct(
        public readonly string $name,
        /** @var string[] */
        public readonly array $lookups = [],
    ) {
        $lookupFilters = [];
        foreach ($this->lookups as $i => $lookup) {
            if (in_array($lookup, self::FILTER_METHODS, true)) {
                $lookupFilters[] = $i;
            }
        }
        $this->lookupFilters = $lookupFilters;
    }

    /**
     * Parses "a.b[0]["c"]" into a name plus its lookups.
     *
     * Hand scanned rather than matched with preg_match_all(): the pattern needed
     * four alternatives with one capture group each, and picking the group that
     * fired used `?:`, which treats a captured "0" as absent -- so "a.0" and
     * a["0"] used to yield an empty lookup. Scanning also rejects trailing junk
     * instead of silently skipping it.
     */
    public static function fromMarkup(string $markup): VariableLookup
    {
        $length = strlen($markup);
        $nameLength = strcspn($markup, '.[');

        if ($nameLength === $length) {
            return new VariableLookup($markup);
        }

        $lookups = [];
        $offset = $nameLength;

        while ($offset < $length) {
            if ($markup[$offset] === '.') {
                $offset++;
                $start = $offset;
                $offset += strspn($markup, self::LOOKUP_WORD, $offset);

                if ($offset === $start) {
                    throw new SyntaxException('Invalid variable lookup: '.substr($markup, $nameLength));
                }

                $lookups[] = substr($markup, $start, $offset - $start);

                continue;
            }

            if ($markup[$offset] !== '[') {
                throw new SyntaxException('Invalid variable lookup: '.substr($markup, $nameLength));
            }

            $offset++;
            $quote = $markup[$offset] ?? '';
            $quoted = $quote === '"' || $quote === "'";

            if ($quoted) {
                $offset++;
            }

            $start = $offset;
            $offset += strspn($markup, $quoted ? self::LOOKUP_WORD : '0123456789', $offset);
            $key = substr($markup, $start, $offset - $start);

            if ($quoted) {
                if (($markup[$offset] ?? '') !== $quote) {
                    throw new SyntaxException('Invalid variable lookup: '.substr($markup, $nameLength));
                }

                $offset++;
            }

            if ($key === '' || ($markup[$offset] ?? '') !== ']') {
                throw new SyntaxException('Invalid variable lookup: '.substr($markup, $nameLength));
            }

            $offset++;
            $lookups[] = $key;
        }

        return new VariableLookup(substr($markup, 0, $nameLength), $lookups);
    }

    public function toString(): string
    {
        if ($this->lookups === []) {
            return $this->name;
        }

        return implode('.', [$this->name, ...$this->lookups]);
    }

    public function __toString(): string
    {
        return $this->toString();
    }

    public function parseTreeVisitorChildren(): array
    {
        return [$this->name, ...$this->lookups];
    }

    public function evaluate(RenderContext $context): mixed
    {
        $name = $context->evaluate($this->name);
        assert(is_string($name));
        $variables = $context->findVariables($name);

        if ($this->lookups === []) {
            if ($context->options->strictVariables && $variables === []) {
                return new UndefinedVariable($this->toString());
            }

            return $variables[0] ?? null;
        }

        foreach ($variables as $object) {
            $object = $context->evaluate($object);

            if ($object instanceof \Generator) {
                $object = iterator_to_array($object, preserve_keys: false);
            }

            foreach ($this->lookups as $i => $lookup) {
                $key = $context->evaluate($lookup) ?? '';

                assert(is_string($key) || is_int($key));

                $nextObject = $context->evaluate($context->internalContextLookup($object, $key));

                if ($nextObject instanceof MissingValue && is_iterable($object) && in_array($i, $this->lookupFilters, true)) {
                    $nextObject = $context->applyFilter($lookup, $object);
                }

                if ($nextObject instanceof MissingValue) {
                    continue 2;
                }

                $object = $nextObject;
                if ($object instanceof IsContextAware) {
                    $object->setContext($context);
                }
            }

            return $object;
        }

        return $context->options->strictVariables ? new UndefinedVariable($this->toString()) : null;
    }
}
