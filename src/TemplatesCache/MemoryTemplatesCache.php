<?php

namespace Keepsuit\Liquid\TemplatesCache;

use Keepsuit\Liquid\Contracts\LiquidTemplatesCache;
use Keepsuit\Liquid\TemplateInterface;

class MemoryTemplatesCache implements LiquidTemplatesCache
{
    /**
     * @var array<string,TemplateInterface>
     */
    protected array $cache = [];

    public function set(string $name, TemplateInterface $template): void
    {
        $this->cache[$name] = $template;
    }

    public function get(string $name): ?TemplateInterface
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

    public function clear(): void
    {
        $this->cache = [];
    }
}
