<?php

namespace Keepsuit\Liquid;

class Drop implements IsContextAware, MapsToLiquid
{
    protected ?Context $context = null;

    public function setContext(Context $context): void
    {
        $this->context = $context;
    }

    public function toLiquid(): mixed
    {
        return $this;
    }

    public function toLiquidValue(): mixed
    {
        return $this;
    }

    public function __toString(): string
    {
        return get_class($this);
    }
}
