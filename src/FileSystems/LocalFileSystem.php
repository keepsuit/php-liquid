<?php

namespace Keepsuit\Liquid\FileSystems;

use Keepsuit\Liquid\Contracts\LiquidFileSystem;
use Keepsuit\Liquid\Exceptions\FileSystemException;

class LocalFileSystem implements LiquidFileSystem
{
    public function __construct(
        protected string $root,
        protected string $pattern = '_%s.liquid'
    ) {
    }

    public function readTemplateFile(string $templatePath): string
    {
        $fullPath = $this->fullPath($templatePath);

        $content = file_get_contents($fullPath);

        if ($content === false) {
            throw new FileSystemException("Template file '$fullPath' not found");
        }

        return $content;
    }

    public function fullPath(string $templatePath): string
    {
        if (! preg_match('/\A[^.\/][a-zA-Z0-9_\/]+\z/', $templatePath)) {
            throw new FileSystemException("Illegal template name '$templatePath'");
        }

        $path = match (true) {
            str_contains($templatePath, '/') => sprintf('%s/%s', dirname($templatePath), sprintf($this->pattern, basename($templatePath))),
            default => sprintf($this->pattern, basename($templatePath)),
        };

        return sprintf('%s/%s', $this->root, $path);
    }
}
