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

    private const LOOKUP_REGEX = '{\.([\w\-]+)|\["([\w\-]+)"\]|\[\'([\w\-]+)\'\]|\[(\d+)\]}';

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
     * Parses `a.b[0]["c"]` into a name plus its lookups.
     */
    public static function fromMarkup(string $markup): VariableLookup
    {
        $nameLength = strcspn($markup, '.[');
        $lookupsString = substr($markup, $nameLength);

        if ($lookupsString === '') {
            return new VariableLookup($markup);
        }

        preg_match_all(self::LOOKUP_REGEX, $lookupsString, $matches, PREG_UNMATCHED_AS_NULL);

        // preg_match_all() skips whatever it cannot match, so the matches have to
        // account for the whole string or there was junk between or after them.
        if (implode('', $matches[0]) !== $lookupsString) {
            throw new SyntaxException('Invalid variable lookup: '.$lookupsString);
        }

        $lookups = [];
        foreach (array_keys($matches[0]) as $i) {
            // Every alternative in the pattern captures, so one of them is set.
            $lookup = $matches[1][$i] ?? $matches[2][$i] ?? $matches[3][$i] ?? $matches[4][$i];
            assert($lookup !== null);

            $lookups[] = $lookup;
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
        $variables = $context->iterateVariables($name);

        if ($this->lookups === []) {
            foreach ($variables as $variable) {
                return $variable;
            }

            return $context->options->strictVariables ? new UndefinedVariable($this->toString()) : null;
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
