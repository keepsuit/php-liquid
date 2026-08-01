<?php

namespace Keepsuit\Liquid;

use Keepsuit\Liquid\Compiler\CompiledTemplateInterface;
use Keepsuit\Liquid\Compiler\Compiler;
use Keepsuit\Liquid\Contracts\LiquidErrorHandler;
use Keepsuit\Liquid\Contracts\LiquidExtension;
use Keepsuit\Liquid\Contracts\LiquidFileSystem;
use Keepsuit\Liquid\Contracts\LiquidTemplatesCache;
use Keepsuit\Liquid\ErrorHandlers\DefaultErrorHandler;
use Keepsuit\Liquid\Exceptions\LiquidException;
use Keepsuit\Liquid\Extensions\StandardExtension;
use Keepsuit\Liquid\FileSystems\BlankFileSystem;
use Keepsuit\Liquid\Parse\ParseContext;
use Keepsuit\Liquid\Render\RenderContext;
use Keepsuit\Liquid\Render\RenderContextOptions;
use Keepsuit\Liquid\Render\ResourceLimits;
use Keepsuit\Liquid\Support\Arr;
use Keepsuit\Liquid\Support\FilterRegistry;
use Keepsuit\Liquid\Support\TagRegistry;
use Keepsuit\Liquid\TemplatesCache\MemoryTemplatesCache;

class Environment
{
    protected static Environment $defaultEnvironment;

    public readonly TagRegistry $tagRegistry;

    public readonly FilterRegistry $filterRegistry;

    public readonly LiquidFileSystem $fileSystem;

    public readonly LiquidErrorHandler $errorHandler;

    public readonly LiquidTemplatesCache $templatesCache;

    public readonly ResourceLimits $defaultResourceLimits;

    public readonly RenderContextOptions $defaultRenderContextOptions;

    /**
     * @var array<class-string<LiquidExtension>, LiquidExtension>
     */
    protected array $extensions = [];

    public function __construct(
        ?LiquidFileSystem $fileSystem = null,
        ?LiquidErrorHandler $errorHandler = null,
        ?LiquidTemplatesCache $templatesCache = null,
        ?ResourceLimits $defaultResourceLimits = null,
        ?RenderContextOptions $defaultRenderContextOptions = null,
        /** @var LiquidExtension[] $extensions */
        array $extensions = [],
    ) {
        $this->tagRegistry = new TagRegistry;
        $this->filterRegistry = new FilterRegistry;
        $this->fileSystem = $fileSystem ?? new BlankFileSystem;
        $this->errorHandler = $errorHandler ?? new DefaultErrorHandler;
        $this->templatesCache = $templatesCache ?? new MemoryTemplatesCache;
        $this->defaultResourceLimits = $defaultResourceLimits ?? new ResourceLimits;
        $this->defaultRenderContextOptions = $defaultRenderContextOptions ?? new RenderContextOptions;

        foreach ($extensions as $extension) {
            $this->addExtension($extension);
        }
    }

    public static function default(): Environment
    {
        if (! isset(self::$defaultEnvironment)) {
            self::$defaultEnvironment = new Environment(
                extensions: [new StandardExtension]
            );
        }

        return self::$defaultEnvironment;
    }

    public function newParseContext(): ParseContext
    {
        return new ParseContext(
            environment: $this,
        );
    }

    public function newRenderContext(
        /**
         * Environment variables only available in the current context
         *
         * @var array<string, mixed> $environment
         */
        array $data = [],
        /**
         * Environment variables that are shared with all sub-contexts
         *
         * @var array<string, mixed> $staticEnvironment
         */
        array $staticData = [],
        /**
         * Registers allows to provide/export data or utilities inside tags
         * Registers are not accessible as variables.
         * Registers are shared with all sub-contexts
         *
         * @var array<string, mixed> $registers
         */
        array $registers = [],
        ?RenderContextOptions $options = null,
        ?ResourceLimits $resourceLimits = null,
    ): RenderContext {
        return new RenderContext(
            data: $data,
            staticData: $staticData,
            registers: $registers,
            options: $options ?? $this->defaultRenderContextOptions,
            resourceLimits: $resourceLimits,
            environment: $this
        );
    }

    /**
     * @throws LiquidException
     */
    public function parseString(string $source, ?string $name = null): Template
    {
        return $this->newParseContext()->parse($source, name: $name);
    }

    /**
     * @throws LiquidException
     */
    public function parseTemplate(string $templateName): Template
    {
        return $this->newParseContext()->parseTemplate($templateName);
    }

    /**
     * Write a requireable compiled artifact for the given template.
     */
    public function compile(Template $template, string $compiledPath): void
    {
        if (! $template instanceof ParsedTemplate) {
            throw new \InvalidArgumentException('Only parsed templates can be compiled.');
        }

        $directory = dirname($compiledPath);

        if (! is_dir($directory) && ! mkdir($directory, 0755, true) && ! is_dir($directory)) {
            throw new \RuntimeException(sprintf('Unable to create compiled template directory: %s', $directory));
        }

        $source = (new Compiler)->compile($template);
        $temporaryPath = tempnam($directory, '.'.basename($compiledPath).'.tmp-');

        if ($temporaryPath === false) {
            throw new \RuntimeException(sprintf('Unable to create temporary compiled template artifact: %s', $compiledPath));
        }

        try {
            $bytesWritten = file_put_contents($temporaryPath, $source);

            if ($bytesWritten !== strlen($source)) {
                throw new \RuntimeException(sprintf('Unable to write compiled template artifact: %s', $compiledPath));
            }

            $compiled = require $temporaryPath;

            if (! $compiled instanceof CompiledTemplateInterface) {
                throw new \RuntimeException(sprintf('Invalid compiled template artifact: %s', $compiledPath));
            }

            $this->publishCompiledArtifact($temporaryPath, $compiledPath);

            if (function_exists('opcache_invalidate')) {
                opcache_invalidate($compiledPath, true);
            }
        } finally {
            if (is_file($temporaryPath)) {
                unlink($temporaryPath);
            }
        }
    }

    protected function publishCompiledArtifact(string $temporaryPath, string $compiledPath): void
    {
        set_error_handler(static fn (): bool => true);

        try {
            $published = rename($temporaryPath, $compiledPath);
        } finally {
            restore_error_handler();
        }

        if (! $published) {
            throw new \RuntimeException(sprintf('Unable to publish compiled template artifact: %s', $compiledPath));
        }
    }

    public function addExtension(LiquidExtension $extension): static
    {
        $this->extensions[$extension::class] = $extension;

        foreach ($extension->getTags() as $tag) {
            $this->tagRegistry->register($tag);
        }

        foreach ($extension->getFiltersProviders() as $filtersProvider) {
            $this->filterRegistry->register($filtersProvider);
        }

        return $this;
    }

    /**
     * @param  class-string<LiquidExtension>  $extensionClass
     */
    public function removeExtension(string $extensionClass): static
    {
        $extension = $this->extensions[$extensionClass] ?? null;

        if ($extension === null) {
            return $this;
        }

        unset($this->extensions[$extensionClass]);

        foreach ($extension->getTags() as $tag) {
            $this->tagRegistry->delete($tag::tagName());
        }

        foreach ($extension->getFiltersProviders() as $filtersProvider) {
            $this->filterRegistry->delete($filtersProvider);
        }

        return $this;
    }

    /**
     * @return array<LiquidExtension>
     */
    public function getExtensions(): array
    {
        return array_values($this->extensions);
    }

    public function getNodeVisitors(): array
    {
        return Arr::flatten(Arr::map(
            $this->getExtensions(),
            fn (LiquidExtension $extension) => $extension->getNodeVisitors()
        ));
    }

    public function getRegisters(): array
    {
        return array_merge(...Arr::map(
            $this->getExtensions(),
            fn (LiquidExtension $extension) => $extension->getRegisters()
        ));
    }
}
