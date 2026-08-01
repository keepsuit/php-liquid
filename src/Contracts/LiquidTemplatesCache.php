<?php

namespace Keepsuit\Liquid\Contracts;

use Keepsuit\Liquid\Template;

interface LiquidTemplatesCache
{
    public function set(string $name, Template $template): void;

    public function get(string $name): ?Template;

    public function has(string $name): bool;

    public function remove(string $name): void;

    public function clear(): void;
}
