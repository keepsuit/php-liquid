<?php

namespace Keepsuit\Liquid\TemplatesCache;

use Keepsuit\Liquid\Compiler\CompiledTemplate;
use Keepsuit\Liquid\Compiler\Compiler;
use Keepsuit\Liquid\ParsedTemplate;
use Keepsuit\Liquid\Template;

class CompiledTemplatesCache extends FilesystemTemplatesCache
{
    /**
     * Keep the instance validated during publication without loading it again.
     */
    public function set(string $name, Template $template): void
    {
        $compiled = $this->compileTemplate($this->getCompiledPath($name), $template);

        if ($this->keepInMemory) {
            $this->cache[$name] = $compiled;
        }
    }

    protected function getCompiledPath(string $name): string
    {
        return parent::getCompiledPath($name).'.php';
    }

    protected function saveCompiledTemplate(string $compiledPath, Template $template): void
    {
        $this->compileTemplate($compiledPath, $template);
    }

    private function compileTemplate(string $compiledPath, Template $template): CompiledTemplate
    {
        if (! $template instanceof ParsedTemplate) {
            throw new \InvalidArgumentException('CompiledTemplatesCache requires a parsed template.');
        }

        return (new Compiler)->compileToFile($template, $compiledPath);
    }

    protected function loadCompiledTemplate(string $compiledPath): ?Template
    {
        try {
            $template = require $compiledPath;

            return $template instanceof CompiledTemplate ? $template : null;
        } catch (\Throwable) {
            return null;
        }
    }
}
