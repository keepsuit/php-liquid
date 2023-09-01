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
use Keepsuit\Liquid\Exceptions\UndefinedVariableException;
use Keepsuit\Liquid\FileSystems\BlankFileSystem;
use Keepsuit\Liquid\Interrupts\Interrupt;
use Keepsuit\Liquid\Parse\Expression;
use Keepsuit\Liquid\Parse\ParseContext;
use Keepsuit\Liquid\Profiler\Profiler;
use Keepsuit\Liquid\Support\Arr;
use Keepsuit\Liquid\Support\FilterRegistry;
use Keepsuit\Liquid\Support\MissingValue;
use Keepsuit\Liquid\TagBlock;
use Keepsuit\Liquid\Template;
use RuntimeException;
use Throwable;

final class Context
{
    protected int $baseScopeDepth = 0;

    protected ?string $templateName = null;

    protected bool $partial = false;

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
        /** @var array<string, mixed> */
        protected array $environment = [],
        /** @var array<string, mixed> $staticEnvironment */
        array $staticEnvironment = [],
        /** array<string, mixed> */
        protected array $outerScope = [],
        array $registers = [],
        protected bool $rethrowExceptions = false,
        public readonly bool $strictVariables = false,
        bool $profile = false,
        protected FilterRegistry $filterRegistry = new FilterRegistry(),
        public readonly ResourceLimits $resourceLimits = new ResourceLimits(),
        public readonly LiquidFileSystem $fileSystem = new BlankFileSystem(),
    ) {
        $this->scopes = [$this->outerScope];

        $this->sharedState = new ContextSharedState(
            staticEnvironment: $staticEnvironment,
            staticRegisters: $registers,
        );

        $this->profiler = $profile ? new Profiler() : null;
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
     * @param  Closure(Context $context): TResult  $closure
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
            return $value->evaluate($this);
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
        $this->scopes[0][$key] = $value;
    }

    public function get(string $key): mixed
    {
        return $this->evaluate(Expression::parse($key));
    }

    public function has(string $key): bool
    {
        return $this->get($key) !== null;
    }

    /**
     * @throws UndefinedVariableException
     */
    public function findVariable(string $key): mixed
    {
        $scope = Arr::first($this->scopes, fn (array $scope) => array_key_exists($key, $scope));

        $variable = is_array($scope)
            ? $this->internalContextLookup($scope, $key)
            : $this->tryFindVariableInEnvironments($key);

        if ($variable instanceof MissingValue) {
            return $this->strictVariables ? throw new UndefinedVariableException($key) : null;
        }

        if ($variable instanceof MapsToLiquid) {
            $variable = $variable->toLiquid();
        }
        if ($variable instanceof IsContextAware) {
            $variable->setContext($this);
        }

        return $variable;
    }

    /**
     * @throws UndefinedVariableException
     */
    public function lookupAndEvaluate(mixed $scope, int|string $key): mixed
    {
        $value = match (true) {
            is_array($scope) || is_object($scope) => $this->internalContextLookup($scope, $key),
            default => new MissingValue(),
        };

        if ($value instanceof MissingValue) {
            return $this->strictVariables ? throw new UndefinedVariableException((string) $key) : null;
        }

        return $value;
    }

    protected function tryFindVariableInEnvironments(string $key): mixed
    {
        $foundVariable = $this->internalContextLookup($this->environment, $key);
        if (! $foundVariable instanceof MissingValue) {
            return $foundVariable;
        }

        return $this->internalContextLookup($this->sharedState->staticEnvironment, $key);
    }

    public function internalContextLookup(array|object $scope, int|string $key): mixed
    {
        $value = match (true) {
            is_array($scope) && array_key_exists($key, $scope) => $scope[$key],
            is_object($scope) => $scope->$key,
            default => new MissingValue(),
        };

        if ($value instanceof Closure) {
            return $this->sharedState->closuresCache[$value] ??= $value($this);
        }

        return $value;
    }

    public function applyFilter(string $filter, mixed $value, mixed ...$args): mixed
    {
        return $this->filterRegistry->invoke($this, $filter, $value, ...$args);
    }

    public function getRegister(string $name): mixed
    {
        return $this->dynamicRegisters[$name] ?? $this->sharedState->staticRegisters[$name] ?? null;
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
    public function handleError(Throwable $error, int $lineNumber = null): string
    {
        $error = match (true) {
            $error instanceof ResourceLimitException => throw $error,
            $error instanceof ArithmeticError => new ArithmeticException($error),
            $error instanceof LiquidException => $error,
            default => new InternalException($error),
        };

        $error->lineNumber = $lineNumber;
        $error->templateName = $this->templateName;

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

    public function loadPartial(ParseContext $parseContext, string $templateName): Template
    {
        $cacheKey = sprintf('%s', $templateName);

        if (Arr::has($this->sharedState->partialsCache, $cacheKey)) {
            return $this->sharedState->partialsCache[$cacheKey];
        }

        $source = $this->fileSystem->readTemplateFile($templateName);

        try {
            $template = $parseContext->partial(function (ParseContext $parseContext) use ($templateName, $source) {
                return Template::parse($parseContext, $source, $templateName);
            });
        } catch (LiquidException $exception) {
            $exception->templateName = $templateName;

            throw $exception;
        }

        $this->sharedState->partialsCache[$cacheKey] = $template;

        return $template;
    }

    /**
     * @throws StackLevelException
     */
    public function newIsolatedSubContext(?string $templateName): Context
    {
        $this->checkOverflow();

        $subContext = new Context(
            filterRegistry: $this->filterRegistry,
            rethrowExceptions: $this->rethrowExceptions,
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
     * @param  Closure(Context $context): TResult  $closure
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
        if ($this->baseScopeDepth + count($this->scopes) > TagBlock::MAX_DEPTH) {
            throw new StackLevelException();
        }
    }
}
