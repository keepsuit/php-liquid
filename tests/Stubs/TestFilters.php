<?php

namespace Keepsuit\Liquid\Tests\Stubs;

class TestFilters
{
    public function hi(string $output): string
    {
        return $output.' hi!';
    }
}
