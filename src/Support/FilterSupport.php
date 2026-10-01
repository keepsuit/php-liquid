<?php

namespace Keepsuit\Liquid\Support;

use Keepsuit\Liquid\Contracts\IsContextAware;
use Keepsuit\Liquid\Drop;
use Keepsuit\Liquid\Exceptions\InvalidArgumentException;
use Keepsuit\Liquid\Render\RenderContext;

/**
 * @internal
 */
class FilterSupport
{
    public static function normalize(mixed $value, RenderContext $context): mixed
    {
        $value = $context->normalizeValue($value);

        if ($value instanceof IsContextAware) {
            $value->setContext($context);
        }

        return $value;
    }

    public static function normalizeCollection(iterable $input, RenderContext $context): array
    {
        return Arr::map(Arr::from($input), fn (mixed $value) => self::normalize($value, $context));
    }

    public static function iterate(mixed $input, RenderContext $context): FilterInputIterator
    {
        return new FilterInputIterator($input, $context);
    }

    public static function stringify(mixed $value): string
    {
        if (is_array($value)) {
            return self::inspectArrayValue($value);
        }

        if (is_float($value) && is_finite($value)) {
            return strtolower((string) json_encode($value, JSON_PRESERVE_ZERO_FRACTION));
        }

        return FilterCoercion::toString($value);
    }

    protected static function inspectArrayValue(mixed $value): string
    {
        if (is_string($value)) {
            return (string) json_encode($value, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_INVALID_UTF8_SUBSTITUTE);
        }

        if ($value === null) {
            return 'nil';
        }

        if (is_array($value)) {
            $list = array_is_list($value);
            $items = [];

            foreach ($value as $key => $item) {
                $items[] = ($list ? '' : self::inspectArrayValue($key).'=>').self::inspectArrayValue($item);
            }

            return ($list ? '[' : '{').implode(', ', $items).($list ? ']' : '}');
        }

        return self::stringify($value);
    }

    public static function canSelectProperty(mixed $item): bool
    {
        return is_array($item) || is_object($item) || is_string($item) || is_int($item);
    }

    /**
     * @phpstan-assert int|string|null $property
     */
    public static function validateProperty(mixed $property): void
    {
        if ($property instanceof UndefinedVariable) {
            throw $property->toException();
        }

        if ($property !== null && ! is_string($property) && ! is_int($property)) {
            throw new InvalidArgumentException('invalid property');
        }
    }

    public static function property(mixed $item, mixed $property, RenderContext $context): mixed
    {
        self::validateProperty($property);
        $value = match (true) {
            is_array($item) => $item[$property ?? ''] ?? null,
            is_string($item) && is_string($property) => str_contains($item, $property) ? $property : null,
            is_string($item) && is_int($property) => $property >= -Str::length($item) && $property < Str::length($item) ? Str::substr($item, $property, 1) : null,
            is_string($item), is_int($item) => throw new InvalidArgumentException('cannot select the property '.FilterCoercion::toString($property)),
            $item instanceof Drop => $item->{(string) $property},
            is_object($item) => $item->{(string) $property} ?? null,
            default => null,
        };

        return self::normalize($value, $context);
    }
}
