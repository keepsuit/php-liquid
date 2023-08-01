<?php

namespace Keepsuit\Liquid;

class Context
{
    protected bool $strictVariables = false;

    /**
     * @var array<array<string, mixed>>
     */
    protected array $scopes;

    public function __construct(
        protected array $environments = [],
        protected array $outerScopes = [],
        protected array $registers = [],
        protected bool $rethrowExceptions = false,
    ) {
        $this->scopes = [...$this->outerScopes];
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

        $variable = $this->lookupAndEvaluate($scope, $key, $throwNotFound);

        return $variable;
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
        return $this->lookupAndEvaluate([], $key, $throwNotFound);
    }
}
