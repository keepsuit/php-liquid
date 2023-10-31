<?php

namespace Keepsuit\Liquid\Contracts;

use Generator;
use Keepsuit\Liquid\Render\Context;

interface CanBeRendered
{
    public function render(Context $context): string;

    /**
     * @return Generator<string>
     */
    public function renderAsync(Context $context): Generator;
}
