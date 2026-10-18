<?php

namespace Keepsuit\Liquid\Support;

use Keepsuit\Liquid\Contracts\IsContextAware;
use Keepsuit\Liquid\Drop;
use Keepsuit\Liquid\Exceptions\InvalidArgumentException;
use Keepsuit\Liquid\Nodes\Range;
use Keepsuit\Liquid\Render\RenderContext;

/**
 * @internal
 */
class FilterSupport
{
    public function __construct(protected RenderContext $context) {}

    public function normalize(mixed $value): mixed
    {
        $value = $this->context->normalizeValue($value);

        if ($value instanceof IsContextAware) {
            $value->setContext($this->context);
        }

        return $value;
    }

    /**
     * Charges one render score point per item before iterating, so oversized collections
     * (e.g. huge ranges) reach the limit before being materialized.
     *
     * @return bool false when the size is unknown and items must be charged while iterating
     */
    public function chargeIteration(mixed $input): bool
    {
        $count = match (true) {
            is_array($input), $input instanceof \Countable => count($input),
            $input instanceof Range => $input->length(),
            default => null,
        };

        if ($count === null) {
            return false;
        }

        $this->context->resourceLimits->incrementRenderScore($count);

        return true;
    }

    public function chargeItem(): void
    {
        $this->context->resourceLimits->incrementRenderScore();
    }

    public function normalizeCollection(iterable $input): array
    {
        if (! $this->chargeIteration($input)) {
            $input = $this->chargedItems($input);
        }

        return Arr::map(Arr::from($input), $this->normalize(...));
    }

    private function chargedItems(iterable $input): \Generator
    {
        foreach ($input as $key => $value) {
            $this->chargeItem();

            yield $key => $value;
        }
    }

    public function iterate(mixed $input): FilterInputIterator
    {
        return new FilterInputIterator($input, $this);
    }

    public function stringify(mixed $value): string
    {
        if (is_array($value)) {
            return $this->inspectArrayValue($value);
        }

        return FilterCoercion::toString($value);
    }

    protected function inspectArrayValue(mixed $value): string
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
                $items[] = ($list ? '' : $this->inspectArrayValue($key).'=>').$this->inspectArrayValue($item);
            }

            return ($list ? '[' : '{').implode(', ', $items).($list ? ']' : '}');
        }

        return $this->stringify($value);
    }

    public function canSelectProperty(mixed $item): bool
    {
        return is_array($item) || is_object($item) || is_string($item) || is_int($item);
    }

    /**
     * @phpstan-assert int|string|null $property
     */
    public function validateProperty(mixed $property): void
    {
        if ($property instanceof UndefinedVariable) {
            throw $property->toException();
        }

        if ($property !== null && ! is_string($property) && ! is_int($property)) {
            throw new InvalidArgumentException('invalid property');
        }
    }

    public function property(mixed $item, mixed $property): mixed
    {
        if ($property !== null && ! is_string($property) && ! is_int($property)) {
            throw $property instanceof UndefinedVariable
                ? $property->toException()
                : new InvalidArgumentException('invalid property');
        }

        $value = match (true) {
            is_array($item) => $item[$property ?? ''] ?? null,
            is_string($item) && is_string($property) => str_contains($item, $property) ? $property : null,
            is_string($item) && is_int($property) => $property >= -Str::length($item) && $property < Str::length($item) ? Str::substr($item, $property, 1) : null,
            is_string($item), is_int($item) => throw new InvalidArgumentException('cannot select the property '.FilterCoercion::toString($property)),
            $item instanceof Drop => $item->{(string) $property},
            is_object($item) => $item->{(string) $property} ?? null,
            default => null,
        };

        return $this->normalize($value);
    }
}
