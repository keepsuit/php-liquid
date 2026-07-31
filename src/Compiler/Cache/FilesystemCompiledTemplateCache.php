<?php

namespace Keepsuit\Liquid\Compiler\Cache;

use Keepsuit\Liquid\Compiler\CompiledTemplateInterface;

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

        return $compiled instanceof CompiledTemplateInterface ? $compiled : null;
    }

    public function has(string $hash): bool
    {
        return is_file($this->getPath($hash));
    }

    public function set(string $hash, string $source): void
    {
        if (file_put_contents($this->getPath($hash), $source) === false) {
            throw new \RuntimeException(sprintf('Unable to write compiled template cache entry: %s', $hash));
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
