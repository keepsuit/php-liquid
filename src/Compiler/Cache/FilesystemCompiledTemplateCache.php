<?php

namespace Keepsuit\Liquid\Compiler\Cache;

use Keepsuit\Liquid\Compiler\CompiledTemplateInterface;
use Keepsuit\Liquid\Template;

class FilesystemCompiledTemplateCache implements CompiledTemplateCache
{
    public function __construct(protected string $cachePath)
    {
        if (! is_dir($this->cachePath) && ! mkdir($this->cachePath, 0755, true) && ! is_dir($this->cachePath)) {
            throw new \RuntimeException(sprintf('Unable to create compiled template cache directory: %s', $this->cachePath));
        }
    }

    public function get(string $hash): ?CompiledTemplateInterface
    {
        if (! $this->has($hash)) {
            return null;
        }

        try {
            $compiled = require $this->getPath($hash);
        } catch (\Throwable) {
            return null;
        }

        return $compiled instanceof Template && $compiled instanceof CompiledTemplateInterface
            ? $compiled
            : null;
    }

    public function has(string $hash): bool
    {
        return is_file($this->getPath($hash));
    }

    public function set(string $hash, string $source): void
    {
        $path = $this->getPath($hash);
        $temporaryPath = tempnam($this->cachePath, '.'.basename($path).'.tmp-');

        if ($temporaryPath === false) {
            throw new \RuntimeException(sprintf('Unable to create temporary compiled template cache entry: %s', $hash));
        }

        try {
            $bytesWritten = file_put_contents($temporaryPath, $source);

            if ($bytesWritten !== strlen($source)) {
                throw new \RuntimeException(sprintf('Unable to write compiled template cache entry: %s', $hash));
            }

            $this->publish($temporaryPath, $path, $hash);

            if (function_exists('opcache_invalidate')) {
                opcache_invalidate($path, true);
            }
        } finally {
            if (is_file($temporaryPath)) {
                unlink($temporaryPath);
            }
        }
    }

    protected function publish(string $temporaryPath, string $path, string $hash): void
    {
        set_error_handler(static fn (): bool => true);

        try {
            $published = rename($temporaryPath, $path);
        } finally {
            restore_error_handler();
        }

        if (! $published) {
            throw new \RuntimeException(sprintf('Unable to publish compiled template cache entry: %s', $hash));
        }
    }

    public function remove(string $hash): void
    {
        $path = $this->getPath($hash);

        if (is_file($path)) {
            unlink($path);
        }
    }

    public function clear(): void
    {
        foreach (glob($this->cachePath.'/*') ?: [] as $path) {
            if (is_file($path)) {
                unlink($path);
            }
        }
    }

    protected function getPath(string $hash): string
    {
        return $this->cachePath.'/'.$hash.'.php';
    }
}
