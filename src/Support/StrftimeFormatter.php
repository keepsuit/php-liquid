<?php

namespace Keepsuit\Liquid\Support;

use DateTimeInterface;
use Keepsuit\Liquid\Exceptions\InvalidArgumentException;
use Keepsuit\Liquid\Exceptions\ResourceLimitException;

/**
 * Ruby-compatible strftime formatting with English names, independent of locale.
 */
final class StrftimeFormatter
{
    public function format(DateTimeInterface $date, string $format, ?int $maxOutputLength = null): string
    {
        $sourceOffset = 0;
        $outputLength = 0;
        $result = preg_replace_callback(
            '/%([-_0^#]*)([1-9][0-9]*)?([EO]?)(:*)(.|$)/s',
            function (array $match) use ($date, $format, $maxOutputLength, &$sourceOffset, &$outputLength): string {
                $matchOffset = strpos($format, $match[0], $sourceOffset);
                $literalLength = $matchOffset - $sourceOffset;
                $this->ensureWithinOutputLimit($literalLength, $outputLength, $maxOutputLength);
                $outputLength += $literalLength;
                $sourceOffset = $matchOffset + strlen($match[0]);

                $append = function (string $value) use ($maxOutputLength, &$outputLength): string {
                    $this->ensureWithinOutputLimit(strlen($value), $outputLength, $maxOutputLength);
                    $outputLength += strlen($value);

                    return $value;
                };

                $flags = $match[1];
                if ($match[2] !== '' && $this->widthExceedsIntegerLimit($match[2])) {
                    return $append($match[0]);
                }
                $width = $match[2] === '' ? null : (int) $match[2];
                $modifier = $match[3];
                $colons = strlen($match[4]);
                $directive = $match[5];

                if ($directive === '' || ($modifier !== '' && $directive === '%')) {
                    throw new InvalidArgumentException('Invalid date format');
                }

                if ($colons > 3 || ($colons > 0 && $directive !== 'z')
                    || ($modifier === 'E' && ! str_contains('cxyCXY', $directive))
                    || ($modifier === 'O' && ! str_contains('deklmuwyHIMSUVW', $directive))) {
                    return $append($match[0]);
                }

                $value = $this->directive($date, $directive, $colons);
                if ($value === null || ($directive === 'Z' && $value === '')) {
                    return $append($value ?? $match[0]);
                }

                if ($maxOutputLength !== null && $width !== null && $this->widthRequiresPadding($directive, $flags)) {
                    $this->ensureWithinOutputLimit($width, $outputLength, $maxOutputLength);
                }

                if (str_contains($flags, '^')) {
                    $value = strtoupper($value);
                }
                if (str_contains($flags, '#')) {
                    $value = match ($directive) {
                        'a', 'A', 'b', 'B', 'h', 'P' => strtoupper($value),
                        'p', 'Z' => strtolower($value),
                        default => $value,
                    };
                }

                if ($directive === 'L' || $directive === 'N') {
                    $fraction = str_pad($date->format('u'), 9, '0');
                    $fractionWidth = $width ?? ($directive === 'L' ? 3 : 9);

                    return $append(substr(str_pad($fraction, $fractionWidth, '0'), 0, $fractionWidth));
                }

                $defaultWidth = match ($directive) {
                    'C', 'd', 'e', 'g', 'H', 'I', 'k', 'l', 'm', 'M', 'S', 'U', 'V', 'W' => 2,
                    'y' => 2,
                    'u', 'w' => 1,
                    'j' => 3,
                    'G', 'Y' => str_starts_with($value, '-') ? 5 : 4,
                    default => 0,
                };
                $padding = ($defaultWidth > 0 || $directive === 's' || $directive === 'z') && ! str_contains('ekl', $directive) ? '0' : ' ';
                foreach (str_split($flags) as $flag) {
                    if ($flag === '_' || $flag === '0') {
                        $padding = $flag === '_' ? ' ' : '0';
                    }
                }

                if ($directive === 'z') {
                    return $append($this->padTimezoneOffset($value, $flags, $width, $padding, $colons));
                }

                if (str_contains($flags, '-') && ! str_contains('cDFvrRTXx', $directive)) {
                    return $append($value);
                }

                $width ??= $defaultWidth;
                if ($padding === '0' && (str_starts_with($value, '-') || str_starts_with($value, '+'))) {
                    return $append($value[0].str_pad(substr($value, 1), max(0, $width - 1), $padding, STR_PAD_LEFT));
                }

                return $append(str_pad($value, $width, $padding, STR_PAD_LEFT));
            },
            $format
        ) ?? $format;

        $suffix = $sourceOffset === 0 ? '' : substr($format, $sourceOffset);
        $this->ensureWithinOutputLimit(strlen($suffix), $outputLength, $maxOutputLength);

        return $result;
    }

    private function widthRequiresPadding(string $directive, string $flags): bool
    {
        return ! str_contains($flags, '-') || str_contains('cDFvrRTXxLNz', $directive);
    }

    private function widthExceedsIntegerLimit(string $width): bool
    {
        $maxWidth = '2147483647';

        return strlen($width) > strlen($maxWidth)
            || (strlen($width) === strlen($maxWidth) && strcmp($width, $maxWidth) > 0);
    }

    private function ensureWithinOutputLimit(int $length, int $outputLength, ?int $maxOutputLength): void
    {
        if ($maxOutputLength !== null && $length > $maxOutputLength - $outputLength) {
            throw new ResourceLimitException;
        }
    }

    private function directive(DateTimeInterface $date, string $directive, int $colons): ?string
    {
        $phpFormat = match ($directive) {
            'a' => 'D', 'A' => 'l', 'b', 'h' => 'M', 'B' => 'F',
            'd', 'e' => 'j', 'u' => 'N', 'w' => 'w', 'V' => 'W',
            'm' => 'n',
            'H', 'k' => 'G', 'I', 'l' => 'g', 'M' => 'i', 'S' => 's',
            'p' => 'A', 'P' => 'a', 's' => 'U',
            default => null,
        };
        if ($phpFormat !== null) {
            $value = $date->format($phpFormat);

            return str_contains('deuwVmGHkIlMSs', $directive) ? (string) (int) $value : $value;
        }

        return match ($directive) {
            '%' => '%', 'n' => "\n", 't' => "\t",
            'C' => (string) (int) floor((int) $date->format('Y') / 100),
            'Y', 'G' => (string) (int) $date->format($directive === 'Y' ? 'Y' : 'o'),
            'g' => (string) (((int) $date->format('o') % 100 + 100) % 100),
            'y' => (string) (((int) $date->format('Y') % 100 + 100) % 100),
            'j' => (string) ((int) $date->format('z') + 1),
            'U' => (string) intdiv((int) $date->format('z') + 7 - (int) $date->format('w'), 7),
            'W' => (string) intdiv((int) $date->format('z') + 7 - ((int) $date->format('N') - 1), 7),
            'L', 'N' => $date->format('u'),
            'c' => $this->format($date, '%a %b %e %T %Y'),
            'D', 'x' => $this->format($date, '%m/%d/%y'),
            'F' => $this->format($date, '%Y-%m-%d'),
            'v' => $this->format($date, '%e-%^b-%Y'),
            'r' => $this->format($date, '%I:%M:%S %p'),
            'R' => $this->format($date, '%H:%M'),
            'T', 'X' => $this->format($date, '%H:%M:%S'),
            'z' => $this->timezoneOffset($date, $colons),
            'Z' => preg_match('/^[+-]/', $date->getTimezone()->getName()) === 1 ? '' : $date->format('T'),
            default => null,
        };
    }

    private function padTimezoneOffset(string $value, string $flags, ?int $width, string $padding, int $colons): string
    {
        $sign = $value[0];
        if (str_contains($flags, '-') && (int) str_replace(':', '', substr($value, 1)) === 0) {
            $sign = '-';
        }

        $parts = explode(':', substr($value, 1));
        $parts[0] = $padding === ' ' ? (string) (int) substr($parts[0], 0, 2).substr($parts[0], 2) : $parts[0];
        $value = $sign.implode(':', $parts);
        $width = max($width ?? 0, match ($colons) {
            0 => 5, 1 => 6, 2 => 9, default => strlen($value),
        });

        return $padding === '0'
            ? $sign.str_pad(substr($value, 1), $width - 1, '0', STR_PAD_LEFT)
            : str_pad($value, $width, ' ', STR_PAD_LEFT);
    }

    private function timezoneOffset(DateTimeInterface $date, int $colons): string
    {
        $offset = $date->getOffset();
        $hours = sprintf('%02d', intdiv(abs($offset), 3600));
        $minutes = sprintf('%02d', intdiv(abs($offset) % 3600, 60));
        $seconds = sprintf('%02d', abs($offset) % 60);

        return ($offset < 0 ? '-' : '+').match ($colons) {
            0 => $hours.$minutes,
            1 => $hours.':'.$minutes,
            2 => $hours.':'.$minutes.':'.$seconds,
            default => $hours.($minutes !== '00' || $seconds !== '00' ? ':'.$minutes : '').($seconds !== '00' ? ':'.$seconds : ''),
        };
    }
}
