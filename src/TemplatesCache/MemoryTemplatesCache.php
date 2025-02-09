<?php

namespace Keepsuit\Liquid\TemplatesCache;

use Keepsuit\Liquid\Contracts\LiquidTemplatesCache;
use Keepsuit\Liquid\Template;

class MemoryTemplatesCache implements LiquidTemplatesCache
{
    /**
     * @var array<string,Template>
     */
    protected array $cache = [];

    public function set(string $name, Template $template): void
    {
        $this->cache[$name] = $template;
    }

    public function get(string $name): ?Template
    {
        return $this->cache[$name] ?? null;
    }

    public function has(string $name): bool
    {
        return isset($this->cache[$name]);
    }

    public function remove(string $name): void
    {
        unset($this->cache[$name]);
    }

    /**
     * @return array<string,Template>
     */
    public function all(): array
    {
        return $this->cache;
    }

    public function clear(): void
    {
        $this->cache = [];
    }
}
