<?php

namespace Keepsuit\Liquid\Render;

use Keepsuit\Liquid\Exceptions\InvalidArgumentException;
use Keepsuit\Liquid\Filters\StandardFilters;
use Keepsuit\Liquid\Support\Str;

class FilterRegistry
{
    /**
     * @var array<string,\Closure>
     */
    protected array $filters = [];

    protected array $filterClasses = [];

    public function __construct(
        protected Context $context
    ) {
        $this->addFilter(StandardFilters::class);
    }

    public static function createWithFilters(Context $context, array $filters): FilterRegistry
    {
        $registry = new self($context);

        foreach ($filters as $filter) {
            $registry->addFilter($filter);
        }

        return $registry;
    }

    /**
     * @param  class-string  $filterClass
     */
    public function addFilter(string $filterClass): static
    {
        if (! class_exists($filterClass)) {
            throw new InvalidArgumentException("Filter class $filterClass does not exist.");
        }

        $reflection = new \ReflectionClass($filterClass);
        foreach ($reflection->getMethods(\ReflectionMethod::IS_PUBLIC) as $method) {
            if (str_starts_with($method->getName(), '__')) {
                continue;
            }

            $this->filters[Str::snake($method->getName())] = function (...$args) use ($filterClass, $method) {
                $filterClassInstance = $this->getFilterClassInstance($filterClass);

                return $filterClassInstance->{$method->getName()}(...$args);
            };
        }

        return $this;
    }

    public function invoke(string $filterName, mixed $value, mixed ...$args): mixed
    {
        $filter = $this->filters[$filterName] ?? null;

        if (! $filter) {
            return $value;
        }

        return $filter($value, ...$args);
    }

    /**
     * @template T of object
     *
     * @param  class-string<T>  $filterClass
     * @return T
     */
    protected function getFilterClassInstance(string $filterClass)
    {
        if (! isset($this->filterClasses[$filterClass])) {
            $this->filterClasses[$filterClass] = new $filterClass($this->context);
        }

        return $this->filterClasses[$filterClass];
    }
}
