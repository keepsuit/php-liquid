<?php

namespace Keepsuit\Liquid;

use Keepsuit\Liquid\Contracts\LiquidFileSystem;
use Keepsuit\Liquid\Exceptions\SyntaxException;
use Keepsuit\Liquid\FileSystems\BlankFileSystem;
use Keepsuit\Liquid\Filters\StandardFilters;
use Keepsuit\Liquid\Parse\ParseContext;
use Keepsuit\Liquid\Render\RenderContext;
use Keepsuit\Liquid\Render\ResourceLimits;
use Keepsuit\Liquid\Support\FilterRegistry;
use Keepsuit\Liquid\Support\TagRegistry;

final class TemplateFactory
{
    protected TagRegistry $tagRegistry;

    protected FilterRegistry $filterRegistry;

    protected LiquidFileSystem $fileSystem;

    protected ResourceLimits $resourceLimits;

    protected bool $profile = false;

    protected bool $rethrowExceptions = false;

    protected bool $strictVariables = false;

    protected bool $allowDynamicPartials = false;

    public function __construct()
    {
        $this->tagRegistry = $this->buildTagRegistry();
        $this->filterRegistry = $this->buildFilterRegistry();
        $this->fileSystem = new BlankFileSystem();
        $this->resourceLimits = new ResourceLimits();
    }

    public static function new(): TemplateFactory
    {
        return new self();
    }

    public function setFilesystem(LiquidFileSystem $fileSystem): TemplateFactory
    {
        $this->fileSystem = $fileSystem;

        return $this;
    }

    public function getFilesystem(): LiquidFileSystem
    {
        return $this->fileSystem;
    }

    public function setResourceLimits(ResourceLimits $resourceLimits): TemplateFactory
    {
        $this->resourceLimits = $resourceLimits;

        return $this;
    }

    public function getResourceLimits(): ResourceLimits
    {
        return $this->resourceLimits;
    }

    public function getTagRegistry(): TagRegistry
    {
        return $this->tagRegistry;
    }

    public function getFilterRegistry(): FilterRegistry
    {
        return $this->filterRegistry;
    }

    public function setProfile(bool $profile = true): TemplateFactory
    {
        $this->profile = $profile;

        return $this;
    }

    public function getProfile(): bool
    {
        return $this->profile;
    }

    public function setRethrowExceptions(bool $rethrowExceptions = true): TemplateFactory
    {
        $this->rethrowExceptions = $rethrowExceptions;

        return $this;
    }

    public function getRethrowExceptions(): bool
    {
        return $this->rethrowExceptions;
    }

    public function setStrictVariables(bool $strictVariables = true): TemplateFactory
    {
        $this->strictVariables = $strictVariables;

        return $this;
    }

    public function getStrictVariables(): bool
    {
        return $this->strictVariables;
    }

    public function setAllowDynamicPartials(bool $allowDynamicPartials = true): TemplateFactory
    {
        $this->allowDynamicPartials = $allowDynamicPartials;

        return $this;
    }

    public function getAllowDynamicPartials(): bool
    {
        return $this->allowDynamicPartials;
    }

    /**
     * Enable/disabled rethrowExceptions and strictVariables.
     */
    public function setDebugMode(bool $debugMode = true): TemplateFactory
    {
        $this->rethrowExceptions = $debugMode;
        $this->strictVariables = $debugMode;

        return $this;
    }

    public function newParseContext(): ParseContext
    {
        return new ParseContext(
            allowDynamicPartials: $this->allowDynamicPartials,
            tagRegistry: $this->tagRegistry,
            fileSystem: $this->fileSystem,
        );
    }

    public function newRenderContext(
        /**
         * Environment variables only available in the current context
         *
         * @var array<string, mixed> $environment
         */
        array $environment = [],
        /**
         * Environment variables that are shared with all sub-contexts
         *
         * @var array<string, mixed> $staticEnvironment
         */
        array $staticEnvironment = [],
        /**
         * Registers allows to provide/export data or utilities inside tags
         * Registers are not accessible as variables.
         * Registers are shared with all sub-contexts
         *
         * @var array<string, mixed> $registers
         */
        array $registers = [],
    ): RenderContext {
        return new RenderContext(
            environment: $environment,
            staticEnvironment: $staticEnvironment,
            registers: $registers,
            rethrowExceptions: $this->rethrowExceptions,
            strictVariables: $this->strictVariables,
            allowDynamicPartials: $this->allowDynamicPartials,
            profile: $this->profile,
            filterRegistry: $this->filterRegistry,
            resourceLimits: $this->resourceLimits,
            fileSystem: $this->fileSystem,
            tagRegistry: $this->tagRegistry,
        );
    }

    /**
     * @throws SyntaxException
     */
    public function parseString(string $source, ?string $name = null): Template
    {
        return Template::parse($this->newParseContext(), $source, $name);
    }

    /**
     * @throws SyntaxException
     */
    public function parseTemplate(string $templateName): Template
    {
        $source = $this->fileSystem->readTemplateFile($templateName);

        return Template::parse($this->newParseContext(), $source, $templateName);
    }

    /**
     * @param  class-string<Tag>  $tag
     */
    public function registerTag(string $tag): TemplateFactory
    {
        $this->tagRegistry->register($tag);

        return $this;
    }

    /**
     * @param  class-string  $filtersProvider
     */
    public function registerFilter(string $filtersProvider): TemplateFactory
    {
        $this->filterRegistry->register($filtersProvider);

        return $this;
    }

    protected function buildTagRegistry(): TagRegistry
    {
        return (new TagRegistry())
            ->register(Tags\AssignTag::class)
            ->register(Tags\BreakTag::class)
            ->register(Tags\CaptureTag::class)
            ->register(Tags\CaseTag::class)
            ->register(Tags\ContinueTag::class)
            ->register(Tags\CycleTag::class)
            ->register(Tags\DecrementTag::class)
            ->register(Tags\EchoTag::class)
            ->register(Tags\ForTag::class)
            ->register(Tags\IfChanged::class)
            ->register(Tags\IfTag::class)
            ->register(Tags\IncrementTag::class)
            ->register(Tags\LiquidTag::class)
            ->register(Tags\RenderTag::class)
            ->register(Tags\TableRowTag::class)
            ->register(Tags\UnlessTag::class);
    }

    protected function buildFilterRegistry(): FilterRegistry
    {
        return (new FilterRegistry())
            ->register(StandardFilters::class);
    }
}
