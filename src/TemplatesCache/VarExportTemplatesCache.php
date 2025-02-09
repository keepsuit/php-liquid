<?php

namespace Keepsuit\Liquid\TemplatesCache;

use Keepsuit\Liquid\Contracts\LiquidTemplatesCache;
use Keepsuit\Liquid\Template;
use Symfony\Component\VarExporter\VarExporter;

class VarExportTemplatesCache implements LiquidTemplatesCache
{
    public function __construct(
        protected string $cachePath
    ) {
        if (! class_exists(VarExporter::class)) {
            throw new \Exception('symfony/var-exporter is required to use VarExportTemplatesCache');
        }

        $this->ensureCacheDirectoryExists();
    }

    public function set(string $name, Template $template): void
    {
        $compiledTemplate = VarExporter::export($template);

        $compiledPath = $this->getCompiledPath($name);

        file_put_contents($compiledPath, '<?php return '.$compiledTemplate.';');

        // Set the timestamp before the startup time to allow opcache to cache the file
        if (is_numeric($_SERVER['REQUEST_TIME'])) {
            touch($compiledPath, ((int) $_SERVER['REQUEST_TIME']) - 5);
        }

        if (function_exists('opcache_invalidate')) {
            opcache_invalidate($compiledPath, true);
        }
    }

    public function get(string $name): ?Template
    {
        if (! $this->has($name)) {
            return null;
        }

        $compiledPath = $this->getCompiledPath($name);

        try {
            $template = require $compiledPath;

            if (! $template instanceof Template) {
                return null;
            }

            return $template;
        } catch (\Throwable) {
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
        return $this->cachePath.'/'.hash('sha256', $name).'.php';
    }

    protected function ensureCacheDirectoryExists(): void
    {
        if (! is_dir($this->cachePath)) {
            mkdir($this->cachePath, 0755, true);
        }
    }
}
