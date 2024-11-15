<?php

namespace Keepsuit\Liquid\Support;

use Keepsuit\Liquid\Contracts\IsContextAware;
use Keepsuit\Liquid\Exceptions\InvalidArgumentException;
use Keepsuit\Liquid\Exceptions\UndefinedFilterException;
use Keepsuit\Liquid\Exceptions\UndefinedVariableException;
use Keepsuit\Liquid\Render\RenderContext;

class FilterRegistry
{
    /**
     * @var array<string,\Closure>
     */
    protected array $filters = [];

    /**
     * @param  class-string  $filterClass
     */
    public function register(string $filterClass): static
    {
        if (! class_exists($filterClass)) {
            throw new InvalidArgumentException("Filter class $filterClass does not exist.");
        }

        $reflection = new \ReflectionClass($filterClass);
        foreach ($reflection->getMethods(\ReflectionMethod::IS_PUBLIC) as $method) {
            if (str_starts_with($method->getName(), '__')) {
                continue;
            }

            $this->filters[Str::snake($method->getName())] = function (RenderContext $context, mixed $value, array $args) use ($filterClass, $method) {
                $filterClassInstance = new $filterClass;

                if ($filterClassInstance instanceof IsContextAware) {
                    $filterClassInstance->setContext($context);
                }

                return $filterClassInstance->{$method->getName()}($value, ...$args);
            };
        }

        return $this;
    }

    /**
     * @throws UndefinedFilterException|UndefinedVariableException
     */
    public function invoke(RenderContext $context, string $filterName, mixed $value, array $args = []): mixed
    {
        $filter = $this->filters[$filterName] ?? null;

        if ($filter !== null) {
            try {
                return $filter($context, $value, $args);
            } catch (\TypeError $e) {
                if ($value instanceof UndefinedVariable) {
                    throw new UndefinedVariableException($value->variableName);
                }

                throw $e;
            }
        }

        if ($context->strictVariables) {
            throw new UndefinedFilterException($filterName);
        }

        return $value;
    }
}
