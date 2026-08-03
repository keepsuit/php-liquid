<?php

namespace Keepsuit\Liquid\Render;

use ArithmeticError;
use Closure;
use Keepsuit\Liquid\Contracts\CanBeEvaluated;
use Keepsuit\Liquid\Contracts\IsContextAware;
use Keepsuit\Liquid\Contracts\LiquidErrorHandler;
use Keepsuit\Liquid\Contracts\MapsToLiquid;
use Keepsuit\Liquid\Drop;
use Keepsuit\Liquid\Drops\SelfDrop;
use Keepsuit\Liquid\Environment;
use Keepsuit\Liquid\ErrorHandlers\RethrowErrorHandler;
use Keepsuit\Liquid\Exceptions\ArithmeticException;
use Keepsuit\Liquid\Exceptions\InternalException;
use Keepsuit\Liquid\Exceptions\LiquidException;
use Keepsuit\Liquid\Exceptions\ResourceLimitException;
use Keepsuit\Liquid\Exceptions\StackLevelException;
use Keepsuit\Liquid\Exceptions\StandardException;
use Keepsuit\Liquid\Exceptions\UndefinedDropMethodException;
use Keepsuit\Liquid\Interrupts\Interrupt;
use Keepsuit\Liquid\Nodes\VariableLookup;
use Keepsuit\Liquid\Parse\ParseContext;
use Keepsuit\Liquid\Support\Arr;
use Keepsuit\Liquid\Support\MissingValue;
use Keepsuit\Liquid\Support\OutputsBag;
use Keepsuit\Liquid\Template;
use RuntimeException;
use Throwable;

final class RenderContext
{
    public readonly Environment $environment;

    public readonly ResourceLimits $resourceLimits;

    protected LiquidErrorHandler $errorHandler;

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

    private ?SelfDrop $selfDrop = null;

    private readonly MissingValue $missingValue;

    public function __construct(
        /**
         * Environment variables only available in the current context
         *
         * @var array<string, mixed>
         */
        protected array $data = [],
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
        public readonly RenderContextOptions $options = new RenderContextOptions,
        ?ResourceLimits $resourceLimits = null,
        ?Environment $environment = null,
        /**
         * Sub-contexts inherit the parent state; building a fresh one here would
         * merge the environment registers only to have it replaced.
         */
        ?ContextSharedState $sharedState = null,
    ) {
        $this->environment = $environment ?? Environment::default();
        $this->resourceLimits = $resourceLimits ?? ResourceLimits::clone($this->environment->defaultResourceLimits);
        $this->errorHandler = $this->options->rethrowErrors ? new RethrowErrorHandler : $this->environment->errorHandler;

        $this->scopes = [[]];

        $this->sharedState = $sharedState ?? new ContextSharedState(
            staticVariables: $staticData,
            registers: array_merge($this->environment->getRegisters(), $registers),
        );
        $this->missingValue = new MissingValue;
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

    /**
     * Execute a generator while keeping a temporary scope active until the
     * generator is fully consumed.
     *
     * @param  Closure(RenderContext): \Generator  $closure
     */
    public function streamStack(Closure $closure): \Generator
    {
        $this->push();

        try {
            yield from $closure($this);
        } finally {
            $this->pop();
        }
    }

    public function evaluate(mixed $value): mixed
    {
        while ($value instanceof CanBeEvaluated) {
            $value = $value->evaluate($this);
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

    /**
     * Resolves $key against the scope chain and returns the innermost value.
     *
     * @return mixed the value, or MissingValue when the key is undefined everywhere
     */
    public function findVariable(string $key): mixed
    {
        // Deliberately not written as a loop over [...$this->scopes, $this->data, ...]:
        // building that list would allocate an array on every variable reference.
        foreach ($this->scopes as $scope) {
            if (array_key_exists($key, $scope)) {
                return $this->resolveVariable($scope[$key]);
            }
        }

        if (array_key_exists($key, $this->data)) {
            return $this->resolveVariable($this->data[$key]);
        }

        if (array_key_exists($key, $this->sharedState->staticVariables)) {
            return $this->resolveVariable($this->sharedState->staticVariables[$key]);
        }

        // Fall back to the implicit self drop only when no value was found anywhere.
        return $key === 'self' ? $this->getSelfDrop() : $this->missingValue;
    }

    /**
     * Every value $key resolves to, innermost scope first.
     *
     * Only useful to callers that need to fall back to an outer scope when the
     * innermost value does not satisfy them; prefer findVariable() otherwise.
     *
     * @return list<mixed>
     */
    public function findVariables(string $key): array
    {
        $variables = [];

        foreach ([...$this->scopes, $this->data, $this->sharedState->staticVariables] as $scope) {
            if (array_key_exists($key, $scope)) {
                $variables[] = $this->resolveVariable($scope[$key]);
            }
        }

        // Fall back to the implicit self drop only when no value was found anywhere.
        if ($variables === [] && $key === 'self') {
            return [$this->getSelfDrop()];
        }

        return $variables;
    }

    /**
     * Normalizes a value pulled out of a scope and binds it to this context.
     */
    protected function resolveVariable(mixed $value): mixed
    {
        // Only objects can need either step, and scalars dominate the hot path.
        if (! is_object($value)) {
            return $value;
        }

        $value = $this->normalizeValue($value);

        if ($value instanceof IsContextAware) {
            $value->setContext($this);
        }

        return $value;
    }

    public function getSelfDrop(): SelfDrop
    {
        return $this->selfDrop ??= new SelfDrop($this);
    }

    public function internalContextLookup(mixed $scope, int|string $key): mixed
    {
        try {
            $value = match (true) {
                is_array($scope) => match (true) {
                    array_key_exists($key, $scope) => $scope[$key],
                    default => $this->missingValue,
                },
                $scope instanceof Drop => $scope->{$key},
                is_object($scope) => match (true) {
                    $this->objectHasProperty($scope, (string) $key) => $scope->{$key},
                    $this->objectHasStaticProperty($scope, (string) $key) => $scope::$$key,
                    default => $this->missingValue,
                },
                default => $this->missingValue,
            };
        } catch (UndefinedDropMethodException) {
            return $this->missingValue;
        }

        return is_object($value) ? $this->normalizeValue($value) : $value;
    }

    protected function objectHasProperty(object $object, string $property): bool
    {
        try {
            // Check if the property is a public property
            if (array_key_exists($property, get_object_vars($object))) {
                return true;
            }

            // Check if the property is accessible via __get() and __isset()
            if (isset($object->{$property})) {
                return true;
            }

            return false;
        } catch (Throwable) {
            return false;
        }
    }

    protected function objectHasStaticProperty(object $object, string $property): bool
    {
        $className = get_class($object);
        $staticProperties = get_class_vars($className);

        return array_key_exists($property, $staticProperties);
    }

    public function normalizeValue(mixed $value): mixed
    {
        // Only objects can need normalization, and scalars dominate the hot path.
        if (! is_object($value)) {
            return $value;
        }

        if ($value instanceof MissingValue) {
            return $value;
        }

        if (isset($this->sharedState->computedObjectsCache[$value])) {
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

    public function applyFilter(string $filter, mixed $value, array $args = []): mixed
    {
        return $this->environment->filterRegistry->invoke($this, $filter, $value, $args);
    }

    public function getRegister(string $name): mixed
    {
        return $this->dynamicRegisters[$name] ?? $this->sharedState->registers[$name] ?? null;
    }

    public function setRegister(string $name, mixed $value): void
    {
        $this->dynamicRegisters[$name] = $value;
    }

    public function getData(string $name): mixed
    {
        return $this->data[$name] ?? null;
    }

    public function setData(string $name, mixed $value): mixed
    {
        return $this->data[$name] = $value;
    }

    public function setToActiveScope(string $key, mixed $value): array
    {
        $index = count($this->scopes) - 1;

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
        return $this->interrupts !== [];
    }

    /**
     * @throws LiquidException
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

        return $this->errorHandler->handle($error);
    }

    public function getErrors(): array
    {
        return $this->sharedState->errors;
    }

    public function getTemplateName(): ?string
    {
        return $this->templateName;
    }

    public function loadPartial(string $templateName): Template
    {
        if ($partial = $this->environment->templatesCache->get($templateName)) {
            return $partial;
        }

        if ($this->options->lazyParsing === false) {
            throw new StandardException(sprintf("The partial '%s' has not be loaded during parsing", $templateName));
        }

        $parseContext = $this->environment->newParseContext();

        $template = $parseContext->loadPartial($templateName);

        $this->sharedState->outputs->merge($template->getState()->outputs);

        return $template;
    }

    public function mergeOutputs(OutputsBag $outputs): RenderContext
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
    public function newIsolatedSubContext(?string $templateName = null, ?RenderContextOptions $options = null): RenderContext
    {
        $this->checkOverflow();

        $subContext = new RenderContext(
            options: $options ?? $this->options,
            resourceLimits: $this->resourceLimits,
            environment: $this->environment,
            sharedState: $this->sharedState,
        );
        $subContext->baseScopeDepth = $this->baseScopeDepth + 1;
        $subContext->templateName = $templateName;
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
