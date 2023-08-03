<?php

namespace Keepsuit\Liquid;

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

    public function __construct(
        array $environment = [],
        protected array $outerScopes = [],
        protected array $registers = [],
        protected bool $rethrowExceptions = false,
        public readonly ResourceLimits $resourceLimits = new ResourceLimits(),
        array $staticEnvironment = [],
    ) {
        $this->scopes = [...$this->outerScopes];
        $this->environments = [$environment];
        $this->staticEnvironments = [$staticEnvironment];
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

        if (! is_array($scope)) {
            return $this->tryFindVariableInEnvironments($key, $throwNotFound);
        }

        return $this->lookupAndEvaluate($scope, $key, $throwNotFound);
    }

    protected function lookupAndEvaluate(array $scope, string $key, bool $throwNotFound = true): mixed
    {
        if ($this->strictVariables && $throwNotFound && ! array_key_exists($key, $scope)) {
            throw new \RuntimeException("Variable `$key` not found");
        }

        return $scope[$key] ?? null;
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
}
