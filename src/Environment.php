<?php

namespace Keepsuit\Liquid;

use Keepsuit\Liquid\Contracts\LiquidErrorHandler;
use Keepsuit\Liquid\Contracts\LiquidExtension;
use Keepsuit\Liquid\Contracts\LiquidFileSystem;
use Keepsuit\Liquid\ErrorHandlers\DefaultErrorHandler;
use Keepsuit\Liquid\Exceptions\LiquidException;
use Keepsuit\Liquid\FileSystems\BlankFileSystem;
use Keepsuit\Liquid\Parse\ParseContext;
use Keepsuit\Liquid\Render\RenderContext;
use Keepsuit\Liquid\Render\RenderContextOptions;
use Keepsuit\Liquid\Render\ResourceLimits;
use Keepsuit\Liquid\Support\Arr;
use Keepsuit\Liquid\Support\FilterRegistry;
use Keepsuit\Liquid\Support\TagRegistry;

class Environment
{
    protected static Environment $defaultEnvironment;

    public readonly TagRegistry $tagRegistry;

    public readonly FilterRegistry $filterRegistry;

    public readonly LiquidFileSystem $fileSystem;

    public readonly LiquidErrorHandler $errorHandler;

    public readonly ResourceLimits $defaultResourceLimits;

    public readonly RenderContextOptions $defaultRenderContextOptions;

    /**
     * @var array<class-string<LiquidExtension>, LiquidExtension>
     */
    protected array $extensions = [];

    public function __construct(
        ?TagRegistry $tagRegistry = null,
        ?FilterRegistry $filterRegistry = null,
        ?LiquidFileSystem $fileSystem = null,
        ?LiquidErrorHandler $errorHandler = null,
        ?ResourceLimits $defaultResourceLimits = null,
        ?RenderContextOptions $defaultRenderContextOptions = null,
        public readonly bool $profile = false,
        /** @var LiquidExtension[] $extensions */
        array $extensions = [],
    ) {
        $this->tagRegistry = $tagRegistry ?? TagRegistry::default();
        $this->filterRegistry = $filterRegistry ?? FilterRegistry::default();
        $this->fileSystem = $fileSystem ?? new BlankFileSystem;
        $this->errorHandler = $errorHandler ?? new DefaultErrorHandler;
        $this->defaultResourceLimits = $defaultResourceLimits ?? new ResourceLimits;
        $this->defaultRenderContextOptions = $defaultRenderContextOptions ?? new RenderContextOptions;
        $this->extensions = Arr::mapWithKeys($extensions, fn (LiquidExtension $extension) => [$extension::class => $extension]);
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
            profile: $this->profile,
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

    public function addExtension(LiquidExtension $extension): static
    {
        $this->extensions[$extension::class] = $extension;

        return $this;
    }

    /**
     * @param  class-string<LiquidExtension>  $extensionClass
     */
    public function removeExtension(string $extensionClass): static
    {
        unset($this->extensions[$extensionClass]);

        return $this;
    }

    /**
     * @return array<LiquidExtension>
     */
    public function getExtensions(): array
    {
        return array_values($this->extensions);
    }

    public function getExtensionNodeVisitors(): array
    {
        return Arr::flatten(Arr::map(
            $this->getExtensions(),
            fn (LiquidExtension $extension) => $extension->getNodeVisitors()
        ));
    }

    public function getExtensionRegisters(): array
    {
        return array_merge(...Arr::map(
            $this->getExtensions(),
            fn (LiquidExtension $extension) => $extension->getRegisters()
        ));
    }
}
