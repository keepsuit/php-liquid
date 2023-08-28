<?php

namespace Keepsuit\Liquid\Tests\Stubs;

use NumberFormatter;

class CanadianMoneyFilter
{
    public function money(string|int|float $input): string
    {
        $formatter = new NumberFormatter('en-US', NumberFormatter::DECIMAL);
        $formatter->setPattern(' #$ CAD');

        return $formatter->formatCurrency($input, 'CAD');
    }
}
