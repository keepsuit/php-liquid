<?php

namespace Keepsuit\Liquid;

class FilterRegistry
{
    /**
     * @var array<string,\Closure>
     */
    protected array $filters = [];

    public function __construct(
        protected Context $context
    ) {
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
            throw new \InvalidArgumentException("Filter class $filterClass does not exist.");
        }

        $reflection = new \ReflectionClass($filterClass);
        foreach ($reflection->getMethods(\ReflectionMethod::IS_STATIC) as $method) {
            if ($method->isPublic()) {
                $this->filters[Str::snake($method->getName())] = fn (...$args) => $filterClass::{$method->getName()}(...$args);
            }
        }

        return $this;
    }

    public function invoke(string $filterName, mixed $value, array ...$args): mixed
    {
        $filter = $this->filters[$filterName] ?? null;

        if (! $filter) {
            return $value;
        }

        return $filter($value, ...$args);
    }
}
