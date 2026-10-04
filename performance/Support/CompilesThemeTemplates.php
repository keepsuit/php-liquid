<?php

namespace Keepsuit\Liquid\Performance\Support;

use Keepsuit\Liquid\Compiler\CompiledTemplate;
use Keepsuit\Liquid\Environment;
use Keepsuit\Liquid\ParsedTemplate;
use Keepsuit\Liquid\Template;
use Keepsuit\Liquid\TemplatesCache\MemoryTemplatesCache;

trait CompilesThemeTemplates
{
    protected function newCompiledEnvironment(): Environment
    {
        return StorefrontTheme::environmentFactory()
            ->setTemplatesCache(new MemoryTemplatesCache)
            ->build();
    }

    protected function compileThemeTemplates(Environment $environment, string $cacheDirectory): void
    {
        $cacheDirectory = $this->prepareCompiledDirectory($cacheDirectory);

        foreach (StorefrontTheme::templateNames() as $templateName) {
            $environment->templatesCache->set($templateName, $this->compileTemplateToPath(
                $environment,
                $environment->parseTemplate($templateName),
                $this->compiledTemplatePath($cacheDirectory, $templateName),
            ));
        }
    }

    protected function compileTemplateToPath(
        Environment $environment,
        Template $template,
        string $artifactPath,
    ): CompiledTemplate {
        assert($template instanceof ParsedTemplate);
        $environment->compile($template, $artifactPath);
        $compiledTemplate = require $artifactPath;

        if (! $compiledTemplate instanceof CompiledTemplate) {
            throw new \RuntimeException("Invalid compiled template benchmark artifact: {$artifactPath}");
        }

        return $compiledTemplate;
    }

    protected function compiledTemplatePath(string $cacheDirectory, string $templateName): string
    {
        return $cacheDirectory.'/'.str_replace('.', '_', $templateName).'.php';
    }

    protected function prepareCompiledDirectory(string $path): string
    {
        if (is_dir($path)) {
            $items = new \FilesystemIterator($path);
            foreach ($items as $item) {
                unlink($item);
            }

            return $path;
        }

        if (! mkdir($path, 0755, true)) {
            throw new \RuntimeException('Could not create the compiled theme benchmark artifact directory.');
        }

        return $path;
    }
}
