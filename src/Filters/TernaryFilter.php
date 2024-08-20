<?php

namespace Keepsuit\Liquid\Filters;

use Keepsuit\Liquid\Contracts\AsLiquidValue;

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
