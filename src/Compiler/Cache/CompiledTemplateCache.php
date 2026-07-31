<?php

namespace Keepsuit\Liquid\Compiler\Cache;

use Keepsuit\Liquid\Compiler\CompiledTemplateInterface;

interface CompiledTemplateCache
{
    public function get(string $hash): ?CompiledTemplateInterface;

    public function has(string $hash): bool;

    public function set(string $hash, string $source): void;

    public function remove(string $hash): void;

    public function clear(): void;
}
