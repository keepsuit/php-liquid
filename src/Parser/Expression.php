<?php

namespace Keepsuit\Liquid\Parser;

use Keepsuit\Liquid\Literal;
use Keepsuit\Liquid\RangeLookup;
use Keepsuit\Liquid\VariableLookup;

class Expression
{
    protected const INTEGERS_REGEX = '/\A(-?\d+)\z/';

    protected const FLOATS_REGEX = '/\A(-?\d[\d\.]+)\z/';

    /**
     * Use an atomic group (?>...) to avoid pathological backtracing from
     * malicious input as described in https://github.com/Shopify/liquid/issues/1357
     */
    protected const RANGES_REGEX = '/\A\(\s*(?>(\S+)\s*\.\.)\s*(\S+)\s*\)\z/';

    protected const LITERALS = [
        'nil' => null,
        'null' => null,
        '' => null,
        'true' => true,
        'false' => false,
        'blank' => Literal::Blank,
        'empty' => Literal::Empty,
    ];

    public static function parse(?string $markup): mixed
    {
        if ($markup === null) {
            return null;
        }

        $markup = trim($markup);

        if (
            (str_starts_with($markup, '"') && str_ends_with($markup, '"')) ||
            (str_starts_with($markup, "'") && str_ends_with($markup, "'"))
        ) {
            return substr($markup, 1, -1);
        }

        if (preg_match(self::INTEGERS_REGEX, $markup, $matches) === 1) {
            return (int) $matches[1];
        }
        if (preg_match(self::RANGES_REGEX, $markup, $matches) === 1) {
            return RangeLookup::parse($matches[1], $matches[2]);
        }
        if (preg_match(self::FLOATS_REGEX, $markup, $matches) === 1) {
            return (float) $matches[1];
        }
        if (array_key_exists($markup, self::LITERALS)) {
            return self::LITERALS[$markup];
        }

        return new VariableLookup($markup);
    }
}
