<?php

namespace Keepsuit\Liquid\Contracts;

use Keepsuit\Liquid\Context;

interface IsContextAware
{
    public function setContext(Context $context): void;
}
