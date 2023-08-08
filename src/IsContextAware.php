<?php

namespace Keepsuit\Liquid;

interface IsContextAware
{
    public function setContext(Context $context): void;
}
