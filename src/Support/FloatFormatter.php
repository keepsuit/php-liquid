<?php

namespace Keepsuit\Liquid\Support;

/**
 * @internal
 */
class FloatFormatter
{
    public static function format(float $value): string
    {
        if (! is_finite($value)) {
            return is_nan($value) ? 'NaN' : ($value < 0 ? '-Infinity' : 'Infinity');
        }

        // var_export yields the shortest round-trip digits (serialize_precision = -1, the PHP default).
        $formatted = var_export($value, true);
        $sign = $formatted[0] === '-' ? '-' : '';
        [$mantissa, $exponent] = array_pad(explode('E', ltrim($formatted, '-')), 2, '0');
        $digits = str_replace('.', '', $mantissa);
        $point = (int) strpos($mantissa, '.') + (int) $exponent;
        $trimmed = ltrim($digits, '0');
        $point -= strlen($digits) - strlen($trimmed);
        $digits = rtrim($trimmed, '0');

        if ($digits === '') {
            return $sign.'0.0';
        }

        // Ruby keeps a fractional part in fixed notation even above 1e15.
        if ($point <= -4 || ($point > 15 && $point >= strlen($digits))) {
            return $sign.$digits[0].'.'.(substr($digits, 1) ?: '0').'e'.sprintf('%+03d', $point - 1);
        }

        $integer = $point > 0 ? str_pad(substr($digits, 0, $point), $point, '0') : '0';
        $fraction = str_repeat('0', max(-$point, 0)).substr($digits, max($point, 0));

        return $sign.$integer.'.'.($fraction ?: '0');
    }
}
