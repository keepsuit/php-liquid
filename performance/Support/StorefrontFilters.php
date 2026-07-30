<?php

namespace Keepsuit\Liquid\Performance\Support;

use Keepsuit\Liquid\Filters\FiltersProvider;

/**
 * Fixture-local filters. Named as a real theme would name them — neither `money`
 * nor `pluralize` collides with a standard filter, and the templates are supposed
 * to read like real Liquid.
 */
final class StorefrontFilters extends FiltersProvider
{
    public function money(int|float $cents): string
    {
        return '€'.number_format($cents / 100, 2, '.', '');
    }

    public function pluralize(int $count, string $singular, string $plural): string
    {
        return $count === 1 ? $singular : $plural;
    }
}
