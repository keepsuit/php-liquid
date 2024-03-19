<?php

namespace Keepsuit\Liquid\Nodes;

use Keepsuit\Liquid\Contracts\CanBeEvaluated;
use Keepsuit\Liquid\Contracts\HasParseTreeVisitorChildren;
use Keepsuit\Liquid\Contracts\IsContextAware;
use Keepsuit\Liquid\Exceptions\SyntaxException;
use Keepsuit\Liquid\Parse\LexerOptions;
use Keepsuit\Liquid\Render\RenderContext;
use Keepsuit\Liquid\Support\Str;

class VariableLookup implements CanBeEvaluated, HasParseTreeVisitorChildren
{
    const FILTER_METHODS = ['size', 'first', 'last'];

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
            if (in_array($lookup, self::FILTER_METHODS)) {
                $lookupFilters[] = $i;
            }
        }
        $this->lookupFilters = $lookupFilters;
    }

    public static function fromMarkup(string $markup): VariableLookup
    {
        $variable = Str::beforeFirst($markup, ['.', '[']);

        $lookupsString = substr($markup, strlen($variable));

        if ($lookupsString === '') {
            return new VariableLookup($variable);
        }

        $count = preg_match_all(LexerOptions::variableLookupRegex(), $lookupsString, $matches);

        if ($count === 0) {
            throw new SyntaxException('Invalid variable lookup: '.$lookupsString);
        }

        $lookups = [];
        foreach (range(0, $count - 1) as $i) {
            $lookups[] = $matches[1][$i] ?: $matches[2][$i] ?: $matches[3][$i] ?: $matches[4][$i];
        }

        return new VariableLookup($variable, $lookups);
    }

    public function toString(): string
    {
        if ($this->lookups === []) {
            return $this->name;
        }

        return sprintf('%s.%s', $this->name, implode('.', $this->lookups));
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
        $object = $context->findVariable($name);

        if ($object instanceof \Generator && $this->lookups !== []) {
            $object = iterator_to_array($object, preserve_keys: false);
        }

        foreach ($this->lookups as $i => $lookup) {
            $key = $context->evaluate($lookup) ?? '';

            assert(is_string($key) || is_int($key));

            $object = match (true) {
                is_array($object) && array_key_exists($key, $object) => $context->lookupAndEvaluate($object, $key),
                is_object($object) => $context->lookupAndEvaluate($object, $key),
                in_array($i, $this->lookupFilters) => $this->applyFilter($context, $object, (string) $key),
                default => $context->lookupAndEvaluate($object, $key),
            };

            if ($object instanceof IsContextAware) {
                $object->setContext($context);
            }
        }

        return $object;
    }

    protected function applyFilter(RenderContext $context, mixed $object, string $filter): mixed
    {
        return match ($filter) {
            'size' => $context->applyFilter('size', $object),
            'first' => $context->applyFilter('first', $object),
            'last' => $context->applyFilter('last', $object),
            default => throw new \RuntimeException(sprintf('Unknown command: %s.', $filter)),
        };
    }
}
