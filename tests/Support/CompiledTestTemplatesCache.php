<?php

namespace Keepsuit\Liquid\Tests\Support;

use Keepsuit\Liquid\Contracts\LiquidTemplatesCache;
use Keepsuit\Liquid\Environment;
use Keepsuit\Liquid\Template;

class CompiledTestTemplatesCache implements LiquidTemplatesCache
{
    public function __construct(private LiquidTemplatesCache $cache, private Environment $environment) {}

    public function set(string $name, Template $template): void
    {
        $this->cache->set($name, CompiledTestEnvironment::compileTemplate($this->environment, $template));
    }

    public function get(string $name): ?Template
    {
        $template = $this->cache->get($name);

        return $template === null ? null : CompiledTestEnvironment::compileTemplate($this->environment, $template);
    }

    public function has(string $name): bool
    {
        return $this->cache->has($name);
    }

    public function remove(string $name): void
    {
        $this->cache->remove($name);
    }

    public function clear(): void
    {
        $this->cache->clear();
    }
}
