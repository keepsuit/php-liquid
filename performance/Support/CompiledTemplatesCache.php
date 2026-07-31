<?php

namespace Keepsuit\Liquid\Performance\Support;

use Keepsuit\Liquid\Compiler\CompiledTemplateInterface;
use Keepsuit\Liquid\TemplateInterface;
use Keepsuit\Liquid\TemplatesCache\FilesystemTemplatesCache;

final class CompiledTemplatesCache extends FilesystemTemplatesCache
{
    public function __construct(string $cachePath)
    {
        parent::__construct($cachePath, keepInMemory: false);
    }

    public function get(string $name): ?TemplateInterface
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

    protected function saveCompiledTemplate(string $compiledPath, TemplateInterface $template): void
    {
        throw new \LogicException('The compiled templates cache is read-only.');
    }

    protected function loadCompiledTemplate(string $compiledPath): ?TemplateInterface
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
