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

        // H is locale independent. Find the shortest significant-digit count
        // that round-trips, without relying on PHP's precision INI settings.
        for ($precision = 1; $precision <= 17; $precision++) {
            $formatted = sprintf('%.*H', $precision, $value);
            if ((float) $formatted === $value) {
                break;
            }
        }

        $sign = str_starts_with($formatted, '-') ? '-' : '';
        $formatted = ltrim($formatted, '-');
        [$mantissa, $exponent] = array_pad(explode('E', $formatted), 2, '0');
        $point = (strpos($mantissa, '.') === false ? strlen($mantissa) : strpos($mantissa, '.')) + (int) $exponent;
        $digits = str_replace('.', '', $mantissa);
        $leadingZeros = strlen($digits) - strlen(ltrim($digits, '0'));
        $point -= $leadingZeros;
        $digits = trim($digits, '0');

        if ($digits === '') {
            return $sign.'0.0';
        }

        // Ruby keeps a fractional part in fixed notation even above 1e15.
        if ($point <= -4 || ($point > 15 && $point >= strlen($digits))) {
            $fraction = substr($digits, 1);

            return $sign.$digits[0].'.'.($fraction === '' ? '0' : $fraction)
                .'e'.sprintf('%+03d', $point - 1);
        }

        if ($point <= 0) {
            return $sign.'0.'.str_repeat('0', -$point).$digits;
        }

        if ($point >= strlen($digits)) {
            return $sign.$digits.str_repeat('0', $point - strlen($digits)).'.0';
        }

        return $sign.substr($digits, 0, $point).'.'.substr($digits, $point);
    }
}
