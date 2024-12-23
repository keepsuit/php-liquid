<?php

namespace Keepsuit\Liquid\Tests\Stubs;

use Keepsuit\Liquid\Filters\FiltersProvider;

class TestFilters extends FiltersProvider
{
    public function hi(string $output): string
    {
        return $output.' hi!';
    }
}
