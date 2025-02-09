<?php

namespace Keepsuit\Liquid\TemplatesCache;

use Keepsuit\Liquid\Template;

abstract class FilesystemTemplatesCache extends MemoryTemplatesCache
{
    public function __construct(
        protected string $cachePath,
        protected bool $keepInMemory = true,
    ) {
        $this->ensureCacheDirectoryExists();
    }

    public function set(string $name, Template $template): void
    {
        if ($this->keepInMemory) {
            parent::set($name, $template);
        }

        $this->saveCompiledTemplate($this->getCompiledPath($name), $template);
    }

    public function get(string $name): ?Template
    {
        if ($this->keepInMemory && $template = parent::get($name)) {
            return $template;
        }

        if (! $this->has($name)) {
            return null;
        }

        $compiledPath = $this->getCompiledPath($name);

        return $this->loadCompiledTemplate($compiledPath);
    }

    public function has(string $name): bool
    {
        return file_exists($this->getCompiledPath($name));
    }

    public function remove(string $name): void
    {
        if ($this->keepInMemory) {
            parent::remove($name);
        }

        unlink($this->getCompiledPath($name));
    }

    public function all(): array
    {
        $files = new \FilesystemIterator($this->cachePath);

        $templates = [];

        foreach ($files as $file) {
            if (! $file instanceof \SplFileInfo) {
                continue;
            }

            try {
                $template = require $file->getPathname();

                if ($template instanceof Template && $template->name() !== null) {
                    $templates[$template->name()] = $template;
                }
            } catch (\Throwable) {
            }
        }

        return $templates;
    }

    public function clear(): void
    {
        if ($this->keepInMemory) {
            parent::clear();
        }

        $files = new \FilesystemIterator($this->cachePath);

        foreach ($files as $file) {
            if ($file instanceof \SplFileInfo) {
                unlink($file->getPathname());
            }
        }
    }

    protected function getCompiledPath(string $name): string
    {
        return $this->cachePath.'/'.hash('sha256', $name);
    }

    protected function ensureCacheDirectoryExists(): void
    {
        if (! is_dir($this->cachePath)) {
            mkdir($this->cachePath, 0755, true);
        }
    }

    abstract protected function saveCompiledTemplate(string $compiledPath, Template $template): void;

    abstract protected function loadCompiledTemplate(string $compiledPath): ?Template;
}
