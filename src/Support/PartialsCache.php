<?php

namespace Keepsuit\Liquid\Support;

use Keepsuit\Liquid\Template;

class PartialsCache
{
    /**
     * @var array<string,Template>
     */
    protected array $cache = [];

    public function set(string $key, Template $value): Template
    {
        $this->cache[$key] = $value;

        return $value;
    }

    public function get(string $key): ?Template
    {
        return $this->cache[$key] ?? null;
    }

    public function has(string $templateName): bool
    {
        return isset($this->cache[$templateName]);
    }

    /**
     * @return array<string,Template>
     */
    public function all(): array
    {
        return $this->cache;
    }

    public function merge(PartialsCache $partialsCache): void
    {
        foreach ($partialsCache->all() as $key => $value) {
            $this->cache[$key] = $value;
        }
    }
}
