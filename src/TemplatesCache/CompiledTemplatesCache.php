<?php

namespace Keepsuit\Liquid\TemplatesCache;

use Keepsuit\Liquid\Compiler\CompiledTemplate;
use Keepsuit\Liquid\Compiler\Compiler;
use Keepsuit\Liquid\ParsedTemplate;
use Keepsuit\Liquid\Template;

class CompiledTemplatesCache extends FilesystemTemplatesCache
{
    /**
     * Only the artifact is written; memory is filled by the next get().
     */
    public function set(string $name, Template $template): void
    {
        unset($this->cache[$name]);

        $this->saveCompiledTemplate($this->getCompiledPath($name), $template);
    }

    protected function getCompiledPath(string $name): string
    {
        return parent::getCompiledPath($name).'.php';
    }

    protected function saveCompiledTemplate(string $compiledPath, Template $template): void
    {
        if (! $template instanceof ParsedTemplate) {
            throw new \InvalidArgumentException('CompiledTemplatesCache requires a parsed template.');
        }

        (new Compiler)->compileToFile($template, $compiledPath);
    }

    protected function loadCompiledTemplate(string $compiledPath): ?Template
    {
        if (! is_file($compiledPath)) {
            return null;
        }

        $template = require $compiledPath;

        return $template instanceof CompiledTemplate ? $template : null;
    }
}
