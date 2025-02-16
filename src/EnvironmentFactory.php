<?php

namespace Keepsuit\Liquid;

use Keepsuit\Liquid\Contracts\LiquidErrorHandler;
use Keepsuit\Liquid\Contracts\LiquidExtension;
use Keepsuit\Liquid\Contracts\LiquidFileSystem;
use Keepsuit\Liquid\Contracts\LiquidTemplatesCache;
use Keepsuit\Liquid\ErrorHandlers\DefaultErrorHandler;
use Keepsuit\Liquid\Extensions\StandardExtension;
use Keepsuit\Liquid\FileSystems\BlankFileSystem;
use Keepsuit\Liquid\Filters\FiltersProvider;
use Keepsuit\Liquid\Render\RenderContextOptions;
use Keepsuit\Liquid\Render\ResourceLimits;
use Keepsuit\Liquid\TemplatesCache\MemoryTemplatesCache;

final class EnvironmentFactory
{
    protected LiquidFileSystem $fileSystem;

    protected LiquidErrorHandler $errorHandler;

    protected LiquidTemplatesCache $templatesCache;

    protected ResourceLimits $resourceLimits;

    protected RenderContextOptions $defaultRenderContextOptions;

    /**
     * @var array<class-string<LiquidExtension>, LiquidExtension>
     */
    protected array $extensions = [];

    /**
     * @var array<class-string<Tag>>
     */
    protected array $tags = [];

    /**
     * @var array<class-string<FiltersProvider>>
     */
    protected array $filters = [];

    public function __construct()
    {
        $this->fileSystem = new BlankFileSystem;
        $this->errorHandler = new DefaultErrorHandler;
        $this->templatesCache = new MemoryTemplatesCache;
        $this->resourceLimits = new ResourceLimits;
        $this->defaultRenderContextOptions = new RenderContextOptions;

        $this->addExtension(new StandardExtension);
    }

    public static function new(): EnvironmentFactory
    {
        return new self;
    }

    public function setFilesystem(LiquidFileSystem $fileSystem): EnvironmentFactory
    {
        $this->fileSystem = $fileSystem;

        return $this;
    }

    public function setErrorHandler(LiquidErrorHandler $errorHandler): EnvironmentFactory
    {
        $this->errorHandler = $errorHandler;

        return $this;
    }

    public function setTemplatesCache(LiquidTemplatesCache $templatesCache): EnvironmentFactory
    {
        $this->templatesCache = $templatesCache;

        return $this;
    }

    public function setResourceLimits(ResourceLimits $resourceLimits): EnvironmentFactory
    {
        $this->resourceLimits = $resourceLimits;

        return $this;
    }

    public function setRethrowErrors(bool $rethrowErrors): EnvironmentFactory
    {
        $this->defaultRenderContextOptions = $this->defaultRenderContextOptions->cloneWith(
            rethrowErrors: $rethrowErrors,
        );

        return $this;
    }

    public function setStrictVariables(bool $strictVariables): EnvironmentFactory
    {
        $this->defaultRenderContextOptions = $this->defaultRenderContextOptions->cloneWith(
            strictVariables: $strictVariables,
        );

        return $this;
    }

    public function setStrictFilters(bool $strictFilters): EnvironmentFactory
    {
        $this->defaultRenderContextOptions = $this->defaultRenderContextOptions->cloneWith(
            strictFilters: $strictFilters,
        );

        return $this;
    }

    public function setLazyParsing(bool $lazyParsing): EnvironmentFactory
    {
        $this->defaultRenderContextOptions = $this->defaultRenderContextOptions->cloneWith(
            lazyParsing: $lazyParsing,
        );

        return $this;
    }

    /**
     * @param  class-string<Tag>  $tag
     */
    public function registerTag(string $tag): EnvironmentFactory
    {
        if (! in_array($tag, $this->tags, true)) {
            $this->tags[] = $tag;
        }

        return $this;
    }

    /**
     * @param  class-string<FiltersProvider>  $filtersProvider
     */
    public function registerFilters(string $filtersProvider): EnvironmentFactory
    {
        if (! in_array($filtersProvider, $this->filters, true)) {
            $this->filters[] = $filtersProvider;
        }

        return $this;
    }

    public function addExtension(LiquidExtension $extension): EnvironmentFactory
    {
        $this->extensions[$extension::class] = $extension;

        return $this;
    }

    public function build(): Environment
    {
        $environment = new Environment(
            fileSystem: $this->fileSystem,
            errorHandler: $this->errorHandler,
            templatesCache: $this->templatesCache,
            defaultResourceLimits: $this->resourceLimits,
            defaultRenderContextOptions: $this->defaultRenderContextOptions,
            extensions: array_values($this->extensions),
        );

        foreach ($this->tags as $tag) {
            $environment->tagRegistry->register($tag);
        }

        foreach ($this->filters as $filtersProvider) {
            $environment->filterRegistry->register($filtersProvider);
        }

        return $environment;
    }
}
