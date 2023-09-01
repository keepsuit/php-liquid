<?php

namespace Keepsuit\Liquid\Tests\Stubs;

use NumberFormatter;

class MoneyFilters
{
    public function money(string|int|float $input): string
    {
        $formatter = new NumberFormatter('en-US', NumberFormatter::DECIMAL);
        $formatter->setPattern(' #¤');

        return $formatter->formatCurrency((float) $input, 'USD') ?: '';
    }

    public function moneyWithUnderscore(string|int|float $input): string
    {
        return $this->money($input);
    }
}
