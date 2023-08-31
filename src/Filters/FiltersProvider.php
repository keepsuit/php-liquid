<?php

namespace Keepsuit\Liquid\Filters;

use Keepsuit\Liquid\Concerns\ContextAware;
use Keepsuit\Liquid\Contracts\IsContextAware;

abstract class FiltersProvider implements IsContextAware
{
    use ContextAware;
}
