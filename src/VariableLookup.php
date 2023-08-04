<?php

namespace Keepsuit\Liquid;

class VariableLookup implements HasParseTreeVisitorChildren, CanBeEvaluated
{
    const COMMAND_METHODS = ['size', 'first', 'last'];

    protected int $commandFlags = 0;

    public readonly mixed $name;

    public readonly array $lookups;

    public function __construct(
        protected string $markup
    ) {
        $lookups = static::markupLookup($markup);

        $this->name = static::parseVariableName(array_shift($lookups));

        foreach ($lookups as $i => $lookup) {
            if (in_array($lookup, self::COMMAND_METHODS)) {
                $this->commandFlags |= 1 << $i;
            }
        }

        $this->lookups = array_map(
            fn ($lookup) => static::parseVariableName($lookup),
            $lookups
        );
    }

    protected static function markupLookup(string $markup): array
    {
        if (preg_match_all(sprintf('/%s/', Regex::VariableParser), $markup, $matches) === false) {
            return [];
        }

        return $matches[0];
    }

    protected static function parseVariableName(string $name): mixed
    {
        if (static::isWrappedInSquareBrackets($name)) {
            return Expression::parse(substr($name, 1, -1));
        }

        return $name;
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

        foreach ($this->lookups as $lookup) {
            $key = $context->evaluate($lookup);

            assert(is_string($key) || is_int($key));

            if (is_array($object) && array_key_exists($key, $object)) {
                $object = $context->lookupAndEvaluate($object, $key);

                continue;
            }

            // TODO: Implement more lookup types.
            throw new \RuntimeException('VariableLookup not implemented yet.');
        }

        return $object;
    }
}
