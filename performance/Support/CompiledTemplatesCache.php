<?php

namespace Keepsuit\Liquid\Performance\Support;

use Keepsuit\Liquid\Compiler\CompiledTemplateInterface;
use Keepsuit\Liquid\Template;
use Keepsuit\Liquid\TemplatesCache\FilesystemTemplatesCache;

final class CompiledTemplatesCache extends FilesystemTemplatesCache
{
    public function __construct(string $cachePath)
    {
        parent::__construct($cachePath, keepInMemory: false);
    }

    public function get(string $name): ?Template
    {
        $compiledPath = $this->getCompiledPath($name);

        if (! is_file($compiledPath)) {
            return null;
        }

        return $this->loadCompiledTemplate($compiledPath);
    }

    public function pathFor(string $name): string
    {
        return $this->getCompiledPath($name);
    }

    protected function getCompiledPath(string $name): string
    {
        return parent::getCompiledPath($name).'.php';
    }

    protected function saveCompiledTemplate(string $compiledPath, Template $template): void
    {
        throw new \LogicException('The compiled templates cache is read-only.');
    }

    protected function loadCompiledTemplate(string $compiledPath): ?Template
    {
        try {
            $template = require $compiledPath;
        } catch (\Throwable) {
            return null;
        }

        return $template instanceof CompiledTemplateInterface
            ? $template
            : null;
    }
}
