<?php

namespace Keepsuit\Liquid\Filters\Custom;

use Keepsuit\Liquid\Contracts\AsLiquidValue;
use Keepsuit\Liquid\Filters\FiltersProvider;

class TernaryFilter extends FiltersProvider
{
    public function ternary(mixed $input, mixed $trueValue, mixed $falseValue): mixed
    {
        $inputValue = $input instanceof AsLiquidValue ? $input->toLiquidValue() : $input;

        return match (true) {
            $inputValue === null, $inputValue === '', $inputValue === [], $inputValue === false => $falseValue,
            default => $trueValue,
        };
    }
}
