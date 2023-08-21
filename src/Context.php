<?php

namespace Keepsuit\Liquid;

use Closure;
use Keepsuit\Liquid\Exceptions\ArithmeticException;
use Keepsuit\Liquid\Exceptions\InternalException;
use Keepsuit\Liquid\Exceptions\LiquidException;
use RuntimeException;

class Context
{
    protected bool $strictVariables = false;

    /**
     * @var array<array<string, mixed>>
     */
    protected array $scopes;

    /**
     * @var array<array<string, mixed>>
     */
    protected array $environments;

    /**
     * @var array<array<string, mixed>>
     */
    protected array $staticEnvironments;

    /**
     * @var array<Interrupt>
     */
    protected array $interrupts = [];

    /**
     * @var array<\Throwable>
     */
    protected array $errors = [];

    protected FilterRegistry $filterRegistry;

    public function __construct(
        array $environment = [],
        protected array $outerScope = [],
        protected array $registers = [],
        protected bool $rethrowExceptions = false,
        public readonly ResourceLimits $resourceLimits = new ResourceLimits(),
        array $staticEnvironment = [],
        /** @var array<class-string> $filters */
        array $filters = [],
    ) {
        $this->scopes = [$this->outerScope];
        $this->environments = [$environment];
        $this->staticEnvironments = [$staticEnvironment];
        $this->filterRegistry = FilterRegistry::createWithFilters($this, $filters);
    }

    protected function push(array $newScope = []): void
    {
        array_unshift($this->scopes, $newScope);
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
     * @param  Closure(): TResult  $closure
     * @return TResult
     */
    public function stack(Closure $closure)
    {
        $this->push();

        try {
            $result = $closure();
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

    public function set(string $key, mixed $value): void
    {
        $this->scopes[0][$key] = $value;
    }

    public function findVariable(string $key, bool $throwNotFound = true): mixed
    {
        $scope = Arr::first($this->scopes, fn (array $scope) => array_key_exists($key, $scope));

        $variable = is_array($scope)
            ? $this->lookupAndEvaluate($scope, $key, $throwNotFound)
            : $this->tryFindVariableInEnvironments($key, $throwNotFound);

        if ($variable instanceof MapsToLiquid) {
            $variable = $variable->toLiquid();
        }
        if ($variable instanceof IsContextAware) {
            $variable->setContext($this);
        }

        return $variable;
    }

    public function lookupAndEvaluate(array|object $scope, int|string $key, bool $throwNotFound = true): mixed
    {
        $fallback = fn (int|string $key) => ($this->strictVariables && $throwNotFound ? throw new RuntimeException("Variable `$key` not found") : null);

        $value = match (true) {
            is_array($scope) => $scope[$key] ?? $fallback($key),
            is_object($scope) => $scope->$key ?? $fallback($key),
        };

        if ($value instanceof Closure) {
            throw new RuntimeException('Cannot evaluate closures');
        }

        return $value;
    }

    protected function tryFindVariableInEnvironments(string $key, bool $throwNotFound = true): mixed
    {
        foreach ($this->environments as $environment) {
            $foundVariable = $this->lookupAndEvaluate($environment, $key, $throwNotFound);
            if ($foundVariable !== null) {
                return $foundVariable;
            }
        }

        foreach ($this->staticEnvironments as $environment) {
            $foundVariable = $this->lookupAndEvaluate($environment, $key, $throwNotFound);
            if ($foundVariable !== null) {
                return $foundVariable;
            }
        }

        return null;
    }

    public function applyFilter(string $filter, mixed $value, mixed ...$args): mixed
    {
        return $this->filterRegistry->invoke($filter, $value, ...$args);
    }

    public function getRegister(string $name): mixed
    {
        return $this->registers[$name] ?? null;
    }

    public function setRegister(string $name, mixed $value): mixed
    {
        return $this->registers[$name] = $value;
    }

    public function getEnvironment(string $name): mixed
    {
        return $this->environments[0][$name] ?? null;
    }

    public function setEnvironment(string $name, mixed $value): mixed
    {
        return $this->environments[0][$name] = $value;
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
     * @throws \Throwable
     */
    public function handleError(\Throwable $error, int $lineNumber = null): string
    {
        $error = match (true) {
            $error instanceof LiquidException => $error,
            $error instanceof \ArithmeticError => new ArithmeticException($error),
            default => new InternalException($error),
        };

        $error->setLineNumber($lineNumber);

        $this->errors[] = $error;

        if ($this->rethrowExceptions) {
            throw $error;
        }

        return (string) $error;
    }
}
