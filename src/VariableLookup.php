<?php

namespace Keepsuit\Liquid;

class VariableLookup
{
    const COMMAND_METHODS = ['size', 'first', 'last'];

    protected int $commandFlags = 0;

    public readonly string $name;

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

    protected static function parseVariableName(string $name): string
    {
        if (static::isWrappedInSquareBrackets($name)) {
            $name = Expression::parse(substr($name, 1, -1));
            assert(is_string($name));
        }

        return $name;
    }

    protected static function isWrappedInSquareBrackets(string $name): bool
    {
        return str_starts_with($name, '[') && str_ends_with($name, ']');
    }

    public function __toString(): string
    {
        return $this->name;
    }
}
