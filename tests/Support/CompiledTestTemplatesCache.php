<?php

namespace Keepsuit\Liquid\Tests\Support;

use Keepsuit\Liquid\Compiler\CompiledTemplate;
use Keepsuit\Liquid\Contracts\LiquidTemplatesCache;
use Keepsuit\Liquid\Environment;
use Keepsuit\Liquid\ParsedTemplate;
use Keepsuit\Liquid\Template;

class CompiledTestTemplatesCache implements LiquidTemplatesCache
{
    public function __construct(private LiquidTemplatesCache $cache, private Environment $environment) {}

    public function set(string $name, Template $template): void
    {
        $this->cache->set($name, $this->compileTemplate($template));
    }

    public function get(string $name): ?Template
    {
        $template = $this->cache->get($name);

        return $template === null ? null : $this->compileTemplate($template);
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

    public function compileTemplate(Template $template): CompiledTemplate
    {
        if ($template instanceof CompiledTemplate) {
            return $template;
        }

        $path = tempnam(sys_get_temp_dir(), 'liquid-test-compiled-');
        if ($path === false) {
            throw new \RuntimeException('Unable to create a compiled test template.');
        }

        try {
            assert($template instanceof ParsedTemplate);
            $this->environment->compile($template, $path);
            $compiled = require $path;
            assert($compiled instanceof CompiledTemplate);

            return $compiled;
        } finally {
            unlink($path);
        }
    }
}
