<?php

namespace Keepsuit\Liquid\Support;

use Keepsuit\Liquid\Exceptions\ArithmeticException;
use Keepsuit\Liquid\Exceptions\InvalidArgumentException;
use Stringable;

class FilterCoercion
{
    public static function toString(mixed $value): string
    {
        if ($value instanceof UndefinedVariable) {
            throw $value->toException();
        }

        return match (true) {
            $value === null => '',
            $value === true => 'true',
            $value === false => 'false',
            is_float($value) => FloatFormatter::format($value),
            is_scalar($value), $value instanceof Stringable => (string) $value,
            default => '',
        };
    }

    public static function toFiniteNumber(mixed $value): int|float
    {
        $number = self::toNumber($value);

        if (is_float($number) && ! is_finite($number)) {
            throw new ArithmeticException(sprintf("Computation results in '%s'%s", self::toString($number), is_nan($number) ? ' (Not a Number)' : ''));
        }

        return $number;
    }

    public static function toNumber(mixed $value): int|float
    {
        if ($value instanceof UndefinedVariable) {
            throw $value->toException();
        }

        if (is_int($value) || is_float($value)) {
            return $value;
        }

        if (! is_string($value)) {
            return 0;
        }

        $value = trim($value);

        if (preg_match('/\A-?\d+\.\d+\z/', $value)) {
            return (float) $value;
        }

        if (preg_match('/\A[+-]?\d+(?:_\d+)*/', $value, $matches)) {
            $number = str_replace('_', '', $matches[0]);

            return is_numeric($number) ? $number + 0 : 0;
        }

        return 0;
    }

    public static function toInteger(mixed $value): int
    {
        if (is_int($value)) {
            return $value;
        }

        if (is_float($value)) {
            throw new InvalidArgumentException('invalid integer');
        }

        $value = trim(self::toString($value));

        if (! preg_match('/\A([+-]?)(0[xX][0-9a-fA-F](?:_?[0-9a-fA-F])*|0[bB][01](?:_?[01])*|0[oO][0-7](?:_?[0-7])*|0[dD]\d(?:_?\d)*|0(?:_?[0-7])*|[1-9](?:_?\d)*)\z/', $value, $matches)) {
            throw new InvalidArgumentException('invalid integer');
        }

        $digits = str_replace('_', '', $matches[2]);
        $base = match (strtolower(substr($digits, 0, 2))) {
            '0x' => 16,
            '0b' => 2,
            '0o' => 8,
            '0d' => 10,
            default => str_starts_with($digits, '0') ? 8 : 10,
        };

        if (in_array(strtolower(substr($digits, 0, 2)), ['0x', '0b', '0o', '0d'], true)) {
            $digits = substr($digits, 2);
        }

        return intval($matches[1].$digits, $base);
    }
}
