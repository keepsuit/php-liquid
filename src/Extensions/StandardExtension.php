<?php

namespace Keepsuit\Liquid\Extensions;

use Keepsuit\Liquid\Filters\StandardFilters;
use Keepsuit\Liquid\Tags;

class StandardExtension extends Extension
{
    public function getTags(): array
    {
        return [
            Tags\AssignTag::class,
            Tags\BreakTag::class,
            Tags\CaptureTag::class,
            Tags\CaseTag::class,
            Tags\ContinueTag::class,
            Tags\CycleTag::class,
            Tags\DecrementTag::class,
            Tags\DocTag::class,
            Tags\EchoTag::class,
            Tags\ForTag::class,
            Tags\IfChanged::class,
            Tags\IfTag::class,
            Tags\IncrementTag::class,
            Tags\LiquidTag::class,
            Tags\RenderTag::class,
            Tags\TableRowTag::class,
            Tags\UnlessTag::class,
        ];
    }

    public function getFiltersProviders(): array
    {
        return [
            StandardFilters::class,
        ];
    }
}
