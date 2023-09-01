<?php

namespace Keepsuit\Liquid;

use Keepsuit\Liquid\Contracts\LiquidFileSystem;
use Keepsuit\Liquid\Exceptions\SyntaxException;
use Keepsuit\Liquid\FileSystems\BlankFileSystem;
use Keepsuit\Liquid\Filters\StandardFilters;
use Keepsuit\Liquid\Parse\ParseContext;
use Keepsuit\Liquid\Render\Context;
use Keepsuit\Liquid\Render\ResourceLimits;
use Keepsuit\Liquid\Support\FilterRegistry;
use Keepsuit\Liquid\Support\I18n;
use Keepsuit\Liquid\Support\TagRegistry;

final class TemplateFactory
{
    public readonly TagRegistry $tagRegistry;

    public readonly FilterRegistry $filterRegistry;

    protected LiquidFileSystem $fileSystem;

    protected ResourceLimits $resourceLimits;

    protected I18n $locale;

    protected bool $profile = false;

    protected bool $lineNumbers = false;

    public function __construct()
    {
        $this->tagRegistry = $this->buildTagRegistry();
        $this->filterRegistry = $this->buildFilterRegistry();
        $this->fileSystem = new BlankFileSystem();
        $this->resourceLimits = new ResourceLimits();
        $this->locale = new I18n();
    }

    public static function new(): TemplateFactory
    {
        return new self();
    }

    public function profile(bool $profile = true): TemplateFactory
    {
        $this->profile = $profile;

        return $this;
    }

    public function lineNumbers(bool $lineNumbers = true): TemplateFactory
    {
        $this->lineNumbers = $lineNumbers;

        return $this;
    }

    public function setFilesystem(LiquidFileSystem $fileSystem): TemplateFactory
    {
        $this->fileSystem = $fileSystem;

        return $this;
    }

    public function setResourceLimits(ResourceLimits $resourceLimits): TemplateFactory
    {
        $this->resourceLimits = $resourceLimits;

        return $this;
    }

    public function setLocale(I18n $locale): TemplateFactory
    {
        $this->locale = $locale;

        return $this;
    }

    public function newParseContext(): ParseContext
    {
        return new ParseContext(
            startLineNumber: $this->lineNumbers || $this->profile,
            tagRegistry: $this->tagRegistry,
            fileSystem: $this->fileSystem,
            locale: $this->locale,
        );
    }

    public function newRenderContext(
        /** @var array<string, mixed> $environment */
        array $environment = [],
        /** @var array<string, mixed> $staticEnvironment */
        array $staticEnvironment = [],
        /** @var array<string, mixed> $outerScope */
        array $outerScope = [],
        /** @var array<string, mixed> $registers */
        array $registers = [],
        bool $rethrowExceptions = false,
        bool $strictVariables = false,
    ): Context {
        return new Context(
            environment: $environment,
            staticEnvironment: $staticEnvironment,
            outerScope: $outerScope,
            registers: $registers,
            rethrowExceptions: $rethrowExceptions,
            strictVariables: $strictVariables,
            profile: $this->profile,
            filterRegistry: $this->filterRegistry,
            resourceLimits: $this->resourceLimits,
            fileSystem: $this->fileSystem,
            locale: $this->locale,
        );
    }

    /**
     * @throws SyntaxException
     */
    public function parseString(string $source, string $name = null): Template
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
            ->register(Tags\CommentTag::class)
            ->register(Tags\ContinueTag::class)
            ->register(Tags\CycleTag::class)
            ->register(Tags\DecrementTag::class)
            ->register(Tags\EchoTag::class)
            ->register(Tags\ForTag::class)
            ->register(Tags\IfChanged::class)
            ->register(Tags\IfTag::class)
            ->register(Tags\IncrementTag::class)
            ->register(Tags\InlineCommentTag::class)
            ->register(Tags\LiquidTag::class)
            ->register(Tags\RawTag::class)
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
