<?php

namespace Keepsuit\Liquid;

interface CanBeRendered
{
    public function render(Context $context): string;
}
