<?php

namespace Keepsuit\Liquid\TemplatesCache;

use Keepsuit\Liquid\Template;
use Symfony\Component\VarExporter\VarExporter;

class VarExportTemplatesCache extends FilesystemTemplatesCache
{
    public function __construct(
        protected string $cachePath,
        protected bool $keepInMemory = true,
    ) {
        if (! class_exists(VarExporter::class)) {
            throw new \Exception('symfony/var-exporter is required to use VarExportTemplatesCache');
        }

        parent::__construct($cachePath, $keepInMemory);
    }

    protected function getCompiledPath(string $name): string
    {
        return parent::getCompiledPath($name).'.php';
    }

    protected function saveCompiledTemplate(string $compiledPath, Template $template): void
    {
        $compiledTemplate = VarExporter::export($template);

        file_put_contents($compiledPath, '<?php return '.$compiledTemplate.';');

        // Set the timestamp before the startup time to allow opcache to cache the file
        if (is_numeric($_SERVER['REQUEST_TIME'])) {
            touch($compiledPath, ((int) $_SERVER['REQUEST_TIME']) - 5);
        }

        if (function_exists('opcache_invalidate')) {
            opcache_invalidate($compiledPath, true);
        }
    }

    protected function loadCompiledTemplate(string $compiledPath): ?Template
    {
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
}
