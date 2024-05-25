<?php

namespace Keepsuit\Liquid\Render;

use ArithmeticError;
use Closure;
use Keepsuit\Liquid\Contracts\CanBeEvaluated;
use Keepsuit\Liquid\Contracts\IsContextAware;
use Keepsuit\Liquid\Contracts\LiquidFileSystem;
use Keepsuit\Liquid\Contracts\MapsToLiquid;
use Keepsuit\Liquid\Exceptions\ArithmeticException;
use Keepsuit\Liquid\Exceptions\InternalException;
use Keepsuit\Liquid\Exceptions\LiquidException;
use Keepsuit\Liquid\Exceptions\ResourceLimitException;
use Keepsuit\Liquid\Exceptions\StackLevelException;
use Keepsuit\Liquid\Exceptions\StandardException;
use Keepsuit\Liquid\Exceptions\UndefinedVariableException;
use Keepsuit\Liquid\FileSystems\BlankFileSystem;
use Keepsuit\Liquid\Interrupts\Interrupt;
use Keepsuit\Liquid\Nodes\VariableLookup;
use Keepsuit\Liquid\Parse\ParseContext;
use Keepsuit\Liquid\Profiler\Profiler;
use Keepsuit\Liquid\Support\Arr;
use Keepsuit\Liquid\Support\FilterRegistry;
use Keepsuit\Liquid\Support\MissingValue;
use Keepsuit\Liquid\Support\OutputsBag;
use Keepsuit\Liquid\Template;
use RuntimeException;
use Throwable;

final class RenderContext
{
    protected int $baseScopeDepth = 0;

    protected ?string $templateName = null;

    public bool $partial = false;

    /**
     * @var array<array<string, mixed>>
     */
    protected array $scopes;

    protected ContextSharedState $sharedState;

    /**
     * @var array<string, mixed>
     */
    protected array $dynamicRegisters = [];

    /**
     * @var array<Interrupt>
     */
    protected array $interrupts = [];

    protected ?Profiler $profiler;

    public function __construct(
        /**
         * Environment variables only available in the current context
         *
         * @var array<string, mixed>
         */
        protected array $environment = [],
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
        public readonly bool $rethrowExceptions = false,
        public readonly bool $strictVariables = false,
        bool $profile = false,
        public readonly FilterRegistry $filterRegistry = new FilterRegistry(),
        public readonly ResourceLimits $resourceLimits = new ResourceLimits(),
        public readonly LiquidFileSystem $fileSystem = new BlankFileSystem(),
    ) {
        $this->scopes = [[]];

        $this->sharedState = new ContextSharedState(
            staticEnvironment: $staticEnvironment,
            registers: $registers,
        );

        $this->profiler = $profile ? new Profiler() : null;
    }

    public function isPartial(): bool
    {
        return $this->partial;
    }

    protected function push(array $newScope = []): void
    {
        array_unshift($this->scopes, $newScope);

        $this->checkOverflow();
    }

    protected function pop(): array
    {
        if (count($this->scopes) === 1) {
            throw new RuntimeException('Cannot pop the outer scope');
        }

        return array_shift($this->scopes) ?? [];
    }

    /**
     * @template TResult
     *
     * @param  Closure(RenderContext $context): TResult  $closure
     * @return TResult
     */
    public function stack(Closure $closure)
    {
        $this->push();

        try {
            $result = $closure($this);
        } finally {
            $this->pop();
        }

        return $result;
    }

    public function evaluate(mixed $value): mixed
    {
        if ($value instanceof CanBeEvaluated) {
            return $this->evaluate($value->evaluate($this));
        }

        return $value;
    }

    /**
     * @param  array<string,mixed>  $values
     */
    public function merge(array $values): void
    {
        $this->scopes[0] = [
            ...$this->scopes[0],
            ...$values,
        ];
    }

    public function set(string $key, mixed $value): void
    {
        Arr::set($this->scopes[0], $key, $value);
    }

    public function get(string $key): mixed
    {
        return $this->evaluate(VariableLookup::fromMarkup($key));
    }

    public function has(string $key): bool
    {
        return $this->get($key) !== null;
    }

    public function findVariables(string $key): array
    {
        $variables = [];

        foreach ($this->scopes as $scope) {
            $variables[] = $this->internalContextLookup($scope, $key);
        }
        $variables[] = $this->internalContextLookup($this->environment, $key);
        $variables[] = $this->internalContextLookup($this->sharedState->staticEnvironment, $key);

        $variables = array_values(array_filter($variables, fn (mixed $value) => ! $value instanceof MissingValue));

        foreach ($variables as $variable) {
            if ($variable instanceof IsContextAware) {
                $variable->setContext($this);
            }
        }

        return $variables;
    }

    public function internalContextLookup(mixed $scope, int|string $key): mixed
    {
        $value = match (true) {
            is_array($scope) && array_key_exists($key, $scope) => $scope[$key],
            is_object($scope) => $scope->$key,
            default => new MissingValue(),
        };

        return $this->normalizeValue($value);
    }

    public function normalizeValue(mixed $value): mixed
    {
        if (is_object($value) && isset($this->sharedState->computedObjectsCache[$value])) {
            return $this->sharedState->computedObjectsCache[$value];
        }

        if ($value instanceof Closure) {
            return $this->sharedState->computedObjectsCache[$value] ??= $this->normalizeValue($value($this));
        }

        if ($value instanceof MapsToLiquid) {
            $liquidValue = $value->toLiquid();

            // Check if toLiquid() returns itself
            return $this->sharedState->computedObjectsCache[$value] ??= match (true) {
                $value === $liquidValue => $value,
                default => $this->normalizeValue($liquidValue)
            };
        }

        return $value;
    }

    public function applyFilter(string $filter, mixed $value, mixed ...$args): mixed
    {
        return $this->filterRegistry->invoke($this, $filter, $value, ...$args);
    }

    public function getRegister(string $name): mixed
    {
        return $this->dynamicRegisters[$name] ?? $this->sharedState->registers[$name] ?? null;
    }

    public function setRegister(string $name, mixed $value): void
    {
        $this->dynamicRegisters[$name] = $value;
    }

    public function getEnvironment(string $name): mixed
    {
        return $this->environment[$name] ?? null;
    }

    public function setEnvironment(string $name, mixed $value): mixed
    {
        return $this->environment[$name] = $value;
    }

    public function setToActiveScope(string $key, mixed $value): array
    {
        $index = array_key_last($this->scopes);

        return $this->scopes[$index] = [
            ...$this->scopes[$index],
            $key => $value,
        ];
    }

    public function pushInterrupt(Interrupt $interrupt): void
    {
        $this->interrupts[] = $interrupt;
    }

    public function popInterrupt(): ?Interrupt
    {
        return array_pop($this->interrupts);
    }

    public function hasInterrupt(): bool
    {
        return count($this->interrupts) > 0;
    }

    /**
     * @throws Throwable
     */
    public function handleError(Throwable $error, ?int $lineNumber = null): string
    {
        $error = match (true) {
            $error instanceof ResourceLimitException => throw $error,
            $error instanceof ArithmeticError => new ArithmeticException($error),
            $error instanceof LiquidException => $error,
            default => new InternalException($error),
        };

        $error->lineNumber = $error->lineNumber ?? $lineNumber;
        $error->templateName = $error->templateName ?? $this->templateName;

        $this->sharedState->errors[] = $error;

        if ($this->rethrowExceptions) {
            throw $error;
        }

        return $error->toLiquidErrorMessage();
    }

    public function getErrors(): array
    {
        return $this->sharedState->errors;
    }

    public function getTemplateName(): ?string
    {
        return $this->templateName;
    }

    public function getProfiler(): ?Profiler
    {
        return $this->profiler;
    }

    public function loadPartial(string $templateName): Template
    {
        if (! Arr::has($this->sharedState->partialsCache, $templateName)) {
            throw new StandardException(sprintf("The partial '%s' has not be loaded during parsing", $templateName));
        }

        return $this->sharedState->partialsCache[$templateName];
    }

    public function setPartialsCache(array $partialsCache): RenderContext
    {
        $this->sharedState->partialsCache = $partialsCache;

        return $this;
    }

    public function mergePartialsCache(array $partialsCache): RenderContext
    {
        $this->sharedState->partialsCache = array_merge($this->sharedState->partialsCache, $partialsCache);

        return $this;
    }

    public function mergeOutputs(array $outputs): RenderContext
    {
        $this->sharedState->outputs->merge($outputs);

        return $this;
    }

    public function getOutputs(): OutputsBag
    {
        return $this->sharedState->outputs;
    }

    /**
     * @throws StackLevelException
     */
    public function newIsolatedSubContext(?string $templateName): RenderContext
    {
        $this->checkOverflow();

        $subContext = new RenderContext(
            rethrowExceptions: $this->rethrowExceptions,
            strictVariables: $this->strictVariables,
            filterRegistry: $this->filterRegistry,
            resourceLimits: $this->resourceLimits,
            fileSystem: $this->fileSystem,
        );
        $subContext->baseScopeDepth = $this->baseScopeDepth + 1;
        $subContext->sharedState = $this->sharedState;
        $subContext->templateName = $templateName;
        $subContext->profiler = $this->profiler;
        $subContext->partial = true;

        return $subContext;
    }

    /**
     * @template TResult
     *
     * @param  string[]  $tags
     * @param  Closure(RenderContext $context): TResult  $closure
     * @return TResult
     */
    public function withDisabledTags(array $tags, Closure $closure)
    {
        foreach ($tags as $tag) {
            $this->sharedState->disabledTags[$tag] = ($this->sharedState->disabledTags[$tag] ?? 0) + 1;
        }

        try {
            $output = $closure($this);
        } finally {
            foreach ($tags as $tag) {
                $this->sharedState->disabledTags[$tag] = max(0, ($this->sharedState->disabledTags[$tag] ?? 0) - 1);
            }
        }

        return $output;
    }

    public function tagDisabled(string $tag): bool
    {
        return ($this->sharedState->disabledTags[$tag] ?? 0) > 0;
    }

    /**
     * @throws StackLevelException
     */
    protected function checkOverflow(): void
    {
        if ($this->baseScopeDepth + count($this->scopes) > ParseContext::MAX_DEPTH) {
            throw StackLevelException::nestingTooDeep();
        }
    }
}
