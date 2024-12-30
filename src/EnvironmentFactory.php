<?php

namespace Keepsuit\Liquid;

use Keepsuit\Liquid\Contracts\LiquidErrorHandler;
use Keepsuit\Liquid\Contracts\LiquidExtension;
use Keepsuit\Liquid\Contracts\LiquidFileSystem;
use Keepsuit\Liquid\ErrorHandlers\DefaultErrorHandler;
use Keepsuit\Liquid\Extensions\StandardExtension;
use Keepsuit\Liquid\FileSystems\BlankFileSystem;
use Keepsuit\Liquid\Filters\FiltersProvider;
use Keepsuit\Liquid\Render\RenderContextOptions;
use Keepsuit\Liquid\Render\ResourceLimits;
use Keepsuit\Liquid\Support\FilterRegistry;
use Keepsuit\Liquid\Support\TagRegistry;

final class EnvironmentFactory
{
    protected TagRegistry $tagRegistry;

    protected FilterRegistry $filterRegistry;

    protected LiquidFileSystem $fileSystem;

    protected LiquidErrorHandler $errorHandler;

    protected ResourceLimits $resourceLimits;

    protected RenderContextOptions $defaultRenderContextOptions;

    /**
     * @var array<class-string<LiquidExtension>, LiquidExtension>
     */
    protected array $extensions = [];

    public function __construct()
    {
        $this->tagRegistry = new TagRegistry;
        $this->filterRegistry = new FilterRegistry;
        $this->fileSystem = new BlankFileSystem;
        $this->errorHandler = new DefaultErrorHandler;
        $this->resourceLimits = new ResourceLimits;
        $this->defaultRenderContextOptions = new RenderContextOptions;

        $this->addExtension(new StandardExtension);
    }

    public static function new(): EnvironmentFactory
    {
        return new self;
    }

    public function setTagRegistry(TagRegistry $tagRegistry): EnvironmentFactory
    {
        $this->tagRegistry = $tagRegistry;

        return $this;
    }

    public function setFilterRegistry(FilterRegistry $filterRegistry): EnvironmentFactory
    {
        $this->filterRegistry = $filterRegistry;

        return $this;
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

    public function setResourceLimits(ResourceLimits $resourceLimits): EnvironmentFactory
    {
        $this->resourceLimits = $resourceLimits;

        return $this;
    }

    public function setRethrowErrors(bool $rethrowErrors = true): EnvironmentFactory
    {
        $this->defaultRenderContextOptions = new RenderContextOptions(
            strictVariables: $this->defaultRenderContextOptions->strictVariables,
            strictFilters: $this->defaultRenderContextOptions->strictFilters,
            rethrowErrors: $rethrowErrors,
        );

        return $this;
    }

    public function setStrictVariables(bool $strictVariables = true): EnvironmentFactory
    {
        $this->defaultRenderContextOptions = new RenderContextOptions(
            strictVariables: $strictVariables,
            strictFilters: $this->defaultRenderContextOptions->strictFilters,
            rethrowErrors: $this->defaultRenderContextOptions->rethrowErrors,
        );

        return $this;
    }

    public function setStrictFilters(bool $strictFilters = true): EnvironmentFactory
    {
        $this->defaultRenderContextOptions = new RenderContextOptions(
            strictVariables: $this->defaultRenderContextOptions->strictVariables,
            strictFilters: $strictFilters,
            rethrowErrors: $this->defaultRenderContextOptions->rethrowErrors,
        );

        return $this;
    }

    /**
     * @param  class-string<Tag>  $tag
     */
    public function registerTag(string $tag): EnvironmentFactory
    {
        $this->tagRegistry->register($tag);

        return $this;
    }

    /**
     * @param  class-string<FiltersProvider>  $filtersProvider
     */
    public function registerFilters(string $filtersProvider): EnvironmentFactory
    {
        $this->filterRegistry->register($filtersProvider);

        return $this;
    }

    public function addExtension(LiquidExtension $extension): EnvironmentFactory
    {
        $this->extensions[$extension::class] = $extension;

        return $this;
    }

    public function build(): Environment
    {
        return new Environment(
            tagRegistry: $this->tagRegistry,
            filterRegistry: $this->filterRegistry,
            fileSystem: $this->fileSystem,
            defaultResourceLimits: $this->resourceLimits,
            defaultRenderContextOptions: $this->defaultRenderContextOptions,
            extensions: array_values($this->extensions),
        );
    }
}
