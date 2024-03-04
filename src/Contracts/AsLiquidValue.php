<?php

namespace Keepsuit\Liquid\Contracts;

interface AsLiquidValue
{
    public function toLiquidValue(): string|int|float|bool|null;
}
