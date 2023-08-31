<?php

namespace Keepsuit\Liquid\Support;

use Keepsuit\Liquid\Contracts\IsContextAware;
use Keepsuit\Liquid\Exceptions\InvalidArgumentException;
use Keepsuit\Liquid\Exceptions\UndefinedFilterException;
use Keepsuit\Liquid\Render\Context;

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

            $this->filters[Str::snake($method->getName())] = function (Context $context, ...$args) use ($filterClass, $method) {
                $filterClassInstance = new $filterClass();

                if ($filterClassInstance instanceof IsContextAware) {
                    $filterClassInstance->setContext($context);
                }

                return $filterClassInstance->{$method->getName()}(...$args);
            };
        }

        return $this;
    }

    /**
     * @throws UndefinedFilterException
     */
    public function invoke(Context $context, string $filterName, mixed $value, mixed ...$args): mixed
    {
        $filter = $this->filters[$filterName] ?? null;

        if ($filter !== null) {
            return $filter($context, $value, ...$args);
        }

        if ($context->strictVariables) {
            throw new UndefinedFilterException($filterName);
        }

        return $value;
    }
}
