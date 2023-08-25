<?php

namespace Keepsuit\Liquid\Contracts;

use Keepsuit\Liquid\Render\Context;

interface IsContextAware
{
    public function setContext(Context $context): void;
}
