<?php

namespace Keepsuit\Liquid;

use Keepsuit\Liquid\Contracts\LiquidFileSystem;
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

    protected ResourceLimits $resourceLimits;

    protected RenderContextOptions $defaultRenderContextOptions;

    protected bool $profile = false;

    public function __construct()
    {
        $this->tagRegistry = TagRegistry::default();
        $this->filterRegistry = FilterRegistry::default();
        $this->fileSystem = new BlankFileSystem;
        $this->resourceLimits = new ResourceLimits;
        $this->defaultRenderContextOptions = new RenderContextOptions;
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

    public function setResourceLimits(ResourceLimits $resourceLimits): EnvironmentFactory
    {
        $this->resourceLimits = $resourceLimits;

        return $this;
    }

    public function setProfile(bool $profile = true): EnvironmentFactory
    {
        $this->profile = $profile;

        return $this;
    }

    public function setRethrowExceptions(bool $rethrowExceptions = true): EnvironmentFactory
    {
        $this->defaultRenderContextOptions->rethrowExceptions = $rethrowExceptions;

        return $this;
    }

    public function setStrictVariables(bool $strictVariables = true): EnvironmentFactory
    {
        $this->defaultRenderContextOptions->strictVariables = $strictVariables;

        return $this;
    }

    public function setStrictFilters(bool $strictFilters = true): EnvironmentFactory
    {
        $this->defaultRenderContextOptions->strictFilters = $strictFilters;

        return $this;
    }

    /**
     * Enable/disabled rethrowExceptions and strictVariables.
     */
    public function setDebugMode(bool $debugMode = true): EnvironmentFactory
    {
        $this->defaultRenderContextOptions->rethrowExceptions = $debugMode;
        $this->defaultRenderContextOptions->strictVariables = $debugMode;
        $this->defaultRenderContextOptions->strictFilters = $debugMode;

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

    public function build(): Environment
    {
        return new Environment(
            tagRegistry: $this->tagRegistry,
            filterRegistry: $this->filterRegistry,
            fileSystem: $this->fileSystem,
            defaultResourceLimits: $this->resourceLimits,
            defaultRenderContextOptions: $this->defaultRenderContextOptions,
            profile: $this->profile,
        );
    }
}
