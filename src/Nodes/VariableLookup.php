<?php

namespace Keepsuit\Liquid\Nodes;

use Keepsuit\Liquid\Contracts\CanBeEvaluated;
use Keepsuit\Liquid\Contracts\HasParseTreeVisitorChildren;
use Keepsuit\Liquid\Contracts\MapsToLiquid;
use Keepsuit\Liquid\Drop;
use Keepsuit\Liquid\Parse\Expression;
use Keepsuit\Liquid\Parse\Regex;
use Keepsuit\Liquid\Render\Context;

class VariableLookup implements HasParseTreeVisitorChildren, CanBeEvaluated
{
    const FILTER_METHODS = ['size', 'first', 'last'];

    public readonly mixed $name;

    /**
     * @var string[]
     */
    public readonly array $lookups;

    /**
     * @var int[]
     */
    protected array $lookupFilters = [];

    public function __construct(
        protected string $markup
    ) {
        $lookups = static::markupLookup($markup);

        $this->name = static::parseVariableName(array_shift($lookups));

        $newLookups = [];
        foreach ($lookups as $i => $lookup) {
            if (in_array($lookup, self::FILTER_METHODS)) {
                $this->lookupFilters[] = $i;
            }

            $newLookups[$i] = self::parseVariableName($lookup);
        }
        $this->lookups = $newLookups;
    }

    protected static function markupLookup(string $markup): array
    {
        if (preg_match_all(sprintf('/%s/', Regex::VariableParser), $markup, $matches) === false) {
            return [];
        }

        return $matches[0];
    }

    protected static function parseVariableName(?string $name): mixed
    {
        return match (true) {
            $name === null => null,
            static::isWrappedInSquareBrackets($name) => Expression::parse(substr($name, 1, -1)),
            default => $name,
        };
    }

    protected static function isWrappedInSquareBrackets(string $name): bool
    {
        return str_starts_with($name, '[') && str_ends_with($name, ']');
    }

    public function __toString(): string
    {
        if (! is_string($this->name)) {
            // TODO: Implement VariableLookup Serialization.
            throw new \RuntimeException('VariableLookup Serialization is not supported yet.');
        }

        return $this->name;
    }

    public function parseTreeVisitorChildren(): array
    {
        return $this->lookups;
    }

    public function evaluate(Context $context): mixed
    {
        $name = $context->evaluate($this->name);
        assert(is_string($name));
        $object = $context->findVariable($name);

        foreach ($this->lookups as $i => $lookup) {
            $key = $context->evaluate($lookup) ?? '';

            assert(is_string($key) || is_int($key));

            $object = match (true) {
                is_array($object) && array_key_exists($key, $object) => $context->lookupAndEvaluate($object, $key),
                is_object($object) => $context->lookupAndEvaluate($object, $key),
                in_array($i, $this->lookupFilters) => $this->applyFilter($context, $object, (string) $key),
                default => null,
            };

            if ($object instanceof MapsToLiquid) {
                $object = $object->toLiquid();
            }

            if ($object instanceof Drop) {
                $object->setContext($context);
            }
        }

        return $object;
    }

    protected function applyFilter(Context $context, mixed $object, string $filter): mixed
    {
        return match ($filter) {
            'size' => $context->applyFilter('size', $object),
            'first' => $context->applyFilter('first', $object),
            'last' => $context->applyFilter('last', $object),
            default => throw new \RuntimeException(sprintf('Unknown command: %s.', $filter)),
        };
    }
}
