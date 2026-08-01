<?php

namespace Keepsuit\Liquid\Performance\Support;

use Keepsuit\Liquid\Compiler\CompiledTemplate;
use Keepsuit\Liquid\Environment;
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

    /**
     * @param  array<string, Template>|null  $templates
     * @return array{templates: array<string, CompiledTemplate>, paths: array<string, string>}
     */
    protected function compileThemeTemplates(
        Environment $environment,
        string $cacheDirectory,
        ?array $templates = null,
    ): array {
        $cacheDirectory = $this->prepareCompiledDirectory($cacheDirectory);
        $compiledTemplates = [];
        $artifactPaths = [];

        foreach (StorefrontTheme::templateNames() as $templateName) {
            $template = $templates[$templateName] ?? $environment->parseTemplate($templateName);
            $artifactPath = $this->compiledTemplatePath($cacheDirectory, $templateName);
            $compiledTemplates[$templateName] = $this->compileTemplateToPath(
                $environment,
                $template,
                $artifactPath,
            );
            $artifactPaths[$templateName] = $artifactPath;
            $environment->templatesCache->set($templateName, $compiledTemplates[$templateName]);
        }

        return [
            'templates' => $compiledTemplates,
            'paths' => $artifactPaths,
        ];
    }

    protected function compileTemplateToPath(
        Environment $environment,
        Template $template,
        string $artifactPath,
    ): CompiledTemplate {
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
