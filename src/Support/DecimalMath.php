<?php

namespace Keepsuit\Liquid\Support;

use Brick\Math\BigDecimal;
use Brick\Math\RoundingMode;
use Closure;

/**
 * Decimal arithmetic matching Shopify Liquid, which converts floats to
 * BigDecimal from their shortest representation and the result back to float.
 *
 * @internal
 */
class DecimalMath
{
    public static function plus(int|float|string $a, int|float|string $b): int|float
    {
        return self::apply($a, $b, fn ($a, $b) => $a + $b, fn (BigDecimal $a, BigDecimal $b) => $a->plus($b));
    }

    public static function minus(int|float|string $a, int|float|string $b): int|float
    {
        return self::apply($a, $b, fn ($a, $b) => $a - $b, fn (BigDecimal $a, BigDecimal $b) => $a->minus($b));
    }

    public static function times(int|float|string $a, int|float|string $b): int|float
    {
        return self::apply($a, $b, fn ($a, $b) => $a * $b, fn (BigDecimal $a, BigDecimal $b) => $a->multipliedBy($b));
    }

    public static function dividedBy(int|float|string $a, int|float|string $b): int|float
    {
        if ($b == 0) {
            return fdiv(self::native($a), self::native($b));
        }

        return self::apply($a, $b, fdiv(...), function (BigDecimal $a, BigDecimal $b) {
            // Divide the unscaled values keeping at least 20 significant digits, then restore the scale.
            $divisor = $b->getUnscaledValue();
            $scale = 20 + strlen((string) $divisor->abs());

            return $a->getUnscaledValue()->toBigDecimal()
                ->dividedBy($divisor, $scale, RoundingMode::HalfEven)
                ->withPointMovedLeft($a->getScale() - $b->getScale());
        });
    }

    public static function modulo(int|float|string $a, int|float|string $b): int|float
    {
        return self::apply($a, $b, fmod(...), function (BigDecimal $a, BigDecimal $b) {
            $remainder = $a->remainder($b);

            return ! $remainder->isZero() && $remainder->isNegative() !== $b->isNegative()
                ? $remainder->plus($b)
                : $remainder;
        });
    }

    /**
     * @param  array<int|float|string>  $numbers
     */
    public static function sum(array $numbers): int|float
    {
        $native = array_sum(array_map(self::native(...), $numbers));
        $total = BigDecimal::zero();
        $hasFloat = false;

        foreach ($numbers as $number) {
            $value = self::native($number);

            if (is_float($value) && ! is_finite($value)) {
                return $native;
            }

            $hasFloat = $hasFloat || is_float($value);
            $total = $total->plus(self::decimal($number));
        }

        return $hasFloat ? self::toFloat($total, $native) : $native;
    }

    /**
     * @param  Closure(int|float, int|float): (int|float)  $native
     * @param  Closure(BigDecimal, BigDecimal): BigDecimal  $operation
     */
    private static function apply(int|float|string $a, int|float|string $b, Closure $native, Closure $operation): int|float
    {
        $nativeA = self::native($a);
        $nativeB = self::native($b);
        $nativeResult = $native($nativeA, $nativeB);

        if ((is_int($nativeA) && is_int($nativeB)) || ! is_finite($nativeA) || ! is_finite($nativeB)) {
            return $nativeResult;
        }

        return self::toFloat($operation(self::decimal($a), self::decimal($b)), $nativeResult);
    }

    private static function toFloat(BigDecimal $result, int|float $native): float
    {
        // BigDecimal has no negative zero, the native result carries the right sign.
        return $result->isZero() ? ($native == 0 ? (float) $native : 0.0) : $result->toFloat();
    }

    /**
     * Numeric strings are kept as-is to preserve digits beyond float precision.
     */
    private static function decimal(int|float|string $value): BigDecimal
    {
        return BigDecimal::of(is_float($value) ? FloatFormatter::format($value) : $value);
    }

    private static function native(int|float|string $value): int|float
    {
        return is_string($value) ? (float) $value : $value;
    }
}
