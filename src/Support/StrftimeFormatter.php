<?php

namespace Keepsuit\Liquid\Support;

use DateTimeInterface;

/**
 * Ruby-compatible strftime formatting with English names, independent of locale.
 * Supports the `-`, `_`, `0` and `^` flags and `%:z`; unsupported directives are left as literal text.
 */
final class StrftimeFormatter
{
    public function format(DateTimeInterface $date, string $format): string
    {
        return preg_replace_callback(
            '/%([-_0^]*)(:?)(.)/s',
            function (array $match) use ($date): string {
                [$literal, $flags, $colon, $directive] = $match;

                $value = $this->directive($date, $directive, $colon === ':');
                if ($value === null) {
                    return $literal;
                }

                if (is_int($value)) {
                    $width = str_contains($flags, '-') ? 0 : match ($directive) {
                        'u', 'w' => 1, 'j' => 3, 'Y', 'G' => 4, default => 2,
                    };
                    $zeroPadded = str_contains($flags, '0') || (! str_contains($flags, '_') && ! str_contains('ekl', $directive));

                    return sprintf($zeroPadded ? '%0*d' : '%*d', $width, $value);
                }

                return str_contains($flags, '^') ? strtoupper($value) : $value;
            },
            $format
        ) ?? $format;
    }

    private function directive(DateTimeInterface $date, string $directive, bool $colon): int|string|null
    {
        $int = fn (string $phpFormat): int => (int) $date->format($phpFormat);

        return match ($directive) {
            'd', 'e' => $int('j'),
            'H', 'k' => $int('G'),
            'I', 'l' => $int('g'),
            'm' => $int('n'),
            'M' => $int('i'),
            'S' => $int('s'),
            'y' => $int('y'),
            'Y' => $int('Y'),
            'G' => $int('o'),
            'g' => $int('o') % 100,
            'C' => intdiv($int('Y'), 100),
            'u' => $int('N'),
            'w' => $int('w'),
            'V' => $int('W'),
            'j' => $int('z') + 1,
            'U' => intdiv($int('z') + 7 - $int('w'), 7),
            'W' => intdiv($int('z') + 7 - ($int('N') - 1), 7),
            'a' => $date->format('D'),
            'A' => $date->format('l'),
            'b', 'h' => $date->format('M'),
            'B' => $date->format('F'),
            'p' => $date->format('A'),
            'P' => $date->format('a'),
            's' => $date->format('U'),
            'L' => $date->format('v'),
            'N' => $date->format('u').'000',
            'z' => $date->format($colon ? 'P' : 'O'),
            'Z' => preg_match('/^[+-]/', $date->getTimezone()->getName()) === 1 ? '' : $date->format('T'),
            'n' => "\n",
            't' => "\t",
            '%' => '%',
            'c' => $this->format($date, '%a %b %e %T %Y'),
            'D', 'x' => $this->format($date, '%m/%d/%y'),
            'F' => $this->format($date, '%Y-%m-%d'),
            'v' => $this->format($date, '%e-%^b-%Y'),
            'r' => $this->format($date, '%I:%M:%S %p'),
            'R' => $this->format($date, '%H:%M'),
            'T', 'X' => $this->format($date, '%H:%M:%S'),
            default => null,
        };
    }
}
