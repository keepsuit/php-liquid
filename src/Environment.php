<?php

namespace Keepsuit\Liquid;

use Keepsuit\Liquid\Contracts\LiquidFileSystem;
use Keepsuit\Liquid\Exceptions\LiquidException;
use Keepsuit\Liquid\FileSystems\BlankFileSystem;
use Keepsuit\Liquid\Parse\ParseContext;
use Keepsuit\Liquid\Render\RenderContext;
use Keepsuit\Liquid\Render\RenderContextOptions;
use Keepsuit\Liquid\Render\ResourceLimits;
use Keepsuit\Liquid\Support\FilterRegistry;
use Keepsuit\Liquid\Support\TagRegistry;

class Environment
{
    protected static Environment $defaultEnvironment;

    public readonly TagRegistry $tagRegistry;

    public readonly FilterRegistry $filterRegistry;

    public readonly LiquidFileSystem $fileSystem;

    public readonly ResourceLimits $defaultResourceLimits;

    public readonly RenderContextOptions $defaultRenderContextOptions;

    public function __construct(
        ?TagRegistry $tagRegistry = null,
        ?FilterRegistry $filterRegistry = null,
        ?LiquidFileSystem $fileSystem = null,
        ?ResourceLimits $defaultResourceLimits = null,
        ?RenderContextOptions $defaultRenderContextOptions = null,
        public readonly bool $profile = false,
    ) {
        $this->tagRegistry = $tagRegistry ?? TagRegistry::default();
        $this->filterRegistry = $filterRegistry ?? FilterRegistry::default();
        $this->fileSystem = $fileSystem ?? new BlankFileSystem;
        $this->defaultResourceLimits = $defaultResourceLimits ?? new ResourceLimits;
        $this->defaultRenderContextOptions = $defaultRenderContextOptions ?? new RenderContextOptions;
    }

    public static function default(): Environment
    {
        if (! isset(self::$defaultEnvironment)) {
            self::$defaultEnvironment = new self;
        }

        return self::$defaultEnvironment;
    }

    public function newParseContext(): ParseContext
    {
        return new ParseContext(
            environment: $this
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
        ?RenderContextOptions $options = null,
        ?ResourceLimits $resourceLimits = null,
    ): RenderContext {
        return new RenderContext(
            variables: $environment,
            staticVariables: $staticEnvironment,
            registers: $registers,
            profile: $this->profile,
            options: $options ?? $this->defaultRenderContextOptions,
            resourceLimits: $resourceLimits ?? clone $this->defaultResourceLimits,
            environment: $this
        );
    }

    /**
     * @throws LiquidException
     */
    public function parseString(string $source, ?string $name = null): Template
    {
        return Template::parse($this->newParseContext(), $source, $name);
    }

    /**
     * @throws LiquidException
     */
    public function parseTemplate(string $templateName): Template
    {
        $source = $this->fileSystem->readTemplateFile($templateName);

        return Template::parse($this->newParseContext(), $source, $templateName);
    }
}
