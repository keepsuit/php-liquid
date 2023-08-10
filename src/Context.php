<?php

namespace Keepsuit\Liquid;

use Closure;
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

        $result = $closure();

        $this->pop();

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

    public function lookupAndEvaluate(array $scope, string $key, bool $throwNotFound = true): mixed
    {
        if ($this->strictVariables && $throwNotFound && ! array_key_exists($key, $scope)) {
            throw new RuntimeException("Variable `$key` not found");
        }

        $value = $scope[$key] ?? null;

        if ($value instanceof Closure) {
            throw new RuntimeException('Cannot evaluate closures');
        }

        return $value;
    }

    protected function tryFindVariableInEnvironments(string $key, bool $throwNotFound = true): mixed
    {
        foreach ($this->environments as $environment) {
            if ($foundVariable = $this->lookupAndEvaluate($environment, $key, $throwNotFound)) {
                return $foundVariable;
            }
        }

        foreach ($this->staticEnvironments as $environment) {
            if ($foundVariable = $this->lookupAndEvaluate($environment, $key, $throwNotFound)) {
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

    public function setToActiveScope(string $key, mixed $value): array
    {
        $index = array_key_last($this->scopes);

        return $this->scopes[$index] = [
            ...$this->scopes[$index],
            $key => $value,
        ];
    }
}
