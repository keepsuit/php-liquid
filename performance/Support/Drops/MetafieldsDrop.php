<?php

namespace Keepsuit\Liquid\Performance\Support\Drops;

use Keepsuit\Liquid\Drop;
use Keepsuit\Liquid\Exceptions\UndefinedDropMethodException;

/**
 * The only drop in the fixture on the liquidMethodMissing path, and the only one
 * with a real reason to be there: metafield keys are arbitrary strings, so they
 * cannot be declared as properties up front.
 *
 * Templates must only read keys that exist. A miss costs up to three thrown and
 * caught exceptions inside Drop::__get, and under strictVariables it is an error
 * the fixture test will fail on.
 */
final class MetafieldsDrop extends Drop
{
    /**
     * @param  array<string, string>  $fields
     */
    public function __construct(
        private readonly array $fields,
    ) {}

    protected function liquidMethodMissing(string $name): mixed
    {
        if (array_key_exists($name, $this->fields)) {
            return $this->fields[$name];
        }

        throw new UndefinedDropMethodException($name);
    }
}
