<?php

namespace Keepsuit\Liquid\Contracts;

use Keepsuit\Liquid\Filters\FiltersProvider;
use Keepsuit\Liquid\Tag;

interface LiquidExtension
{
    /**
     * @return array<class-string<Tag>>
     */
    public function getTags(): array;

    /**
     * @return array<class-string<FiltersProvider>>
     */
    public function getFiltersProviders(): array;

    /**
     * @return array<string,mixed>
     */
    public function getRegisters(): array;

    /**
     * @return array<NodeVisitor>
     */
    public function getNodeVisitors(): array;
}
