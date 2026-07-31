<?php

namespace Keepsuit\Liquid\Contracts;

use Keepsuit\Liquid\TemplateInterface;

interface LiquidTemplatesCache
{
    public function set(string $name, TemplateInterface $template): void;

    public function get(string $name): ?TemplateInterface;

    public function has(string $name): bool;

    public function remove(string $name): void;

    public function clear(): void;
}
