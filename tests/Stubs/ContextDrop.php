<?php

namespace Keepsuit\Liquid\Tests\Stubs;

use Keepsuit\Liquid\Drop;

class ContextDrop extends Drop
{
    public function scopes(): int
    {
        return count(invade($this->context)->scopes);
    }

    public function scopesAsArray(): array
    {
        return range(1, count(invade($this->context)->scopes));
    }

    public function loopPos(): ?int
    {
        return $this->context->get('forloop.index');
    }

    protected function liquidMethodMissing(string $name): mixed
    {
        return $this->context->get($name);
    }
}
