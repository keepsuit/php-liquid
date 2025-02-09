<?php

namespace Keepsuit\Liquid\TemplatesCache;

use Keepsuit\Liquid\Contracts\LiquidTemplatesCache;
use Keepsuit\Liquid\Template;

class SerializeTemplatesCache implements LiquidTemplatesCache
{
    public function __construct(
        protected string $cachePath
    ) {
        $this->ensureCacheDirectoryExists();
    }

    public function set(string $name, Template $template): void
    {
        $compiledPath = $this->getCompiledPath($name);

        file_put_contents($compiledPath, serialize($template));
    }

    public function get(string $name): ?Template
    {
        if (! $this->has($name)) {
            return null;
        }

        try {
            $content = file_get_contents($this->getCompiledPath($name));

            if ($content === false) {
                return null;
            }

            $template = unserialize($content);

            if (! $template instanceof Template) {
                return null;
            }

            return $template;
        } catch (\Exception $e) {
            return null;
        }
    }

    public function has(string $name): bool
    {
        return file_exists($this->getCompiledPath($name));
    }

    public function remove(string $name): void
    {
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
}
