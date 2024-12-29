<?php

namespace Keepsuit\Liquid\Support;

use Keepsuit\Liquid\Attributes\Hidden;
use Keepsuit\Liquid\Contracts\IsContextAware;
use Keepsuit\Liquid\Exceptions\InvalidArgumentException;
use Keepsuit\Liquid\Exceptions\UndefinedFilterException;
use Keepsuit\Liquid\Exceptions\UndefinedVariableException;
use Keepsuit\Liquid\Filters\FiltersProvider;
use Keepsuit\Liquid\Render\RenderContext;

class FilterRegistry
{
    /**
     * @var array<string,array{0:FiltersProvider,1:string}>
     */
    protected array $filters = [];

    /**
     * @param  class-string<FiltersProvider>  $filtersClass
     */
    public function register(string $filtersClass): static
    {
        if (! class_exists($filtersClass)) {
            throw new InvalidArgumentException("Filter class $filtersClass does not exist.");
        }

        $filters = $this->extractFiltersFromClass($filtersClass);

        $instance = new $filtersClass;
        foreach ($filters as $filter => $method) {
            $this->filters[$filter] = [$instance, $method];
        }

        return $this;
    }

    /**
     * @param  class-string<FiltersProvider>  $filtersClass
     */
    public function delete(string $filtersClass): static
    {
        if (! class_exists($filtersClass)) {
            return $this;
        }

        $filters = $this->extractFiltersFromClass($filtersClass);

        foreach ($filters as $key => $value) {
            unset($this->filters[$key]);
        }

        return $this;
    }

    /**
     * @return string[]
     */
    public function all(): array
    {
        return array_keys($this->filters);
    }

    public function has(string $filter): bool
    {
        return isset($this->filters[$filter]);
    }

    /**
     * @throws UndefinedFilterException|UndefinedVariableException
     */
    public function invoke(RenderContext $context, string $filterName, mixed $value, array $args = []): mixed
    {
        $filter = $this->filters[$filterName] ?? null;

        if ($filter !== null) {
            try {
                $instance = $filter[0];
                $method = $filter[1];

                if ($instance instanceof IsContextAware) {
                    $instance->setContext($context);
                }

                return $instance->{$method}($value, ...$args);
            } catch (\TypeError $e) {
                if ($value instanceof UndefinedVariable) {
                    throw new UndefinedVariableException($value->variableName);
                }

                throw $e;
            }
        }

        if ($context->options->strictFilters) {
            throw new UndefinedFilterException($filterName);
        }

        return $value;
    }

    /**
     * @param  class-string<FiltersProvider>  $filtersClass
     * @return array<string,string>
     */
    protected function extractFiltersFromClass(string $filtersClass): array
    {
        $filters = [];

        $reflection = new \ReflectionClass($filtersClass);
        foreach ($reflection->getMethods(\ReflectionMethod::IS_PUBLIC) as $method) {
            if (str_starts_with($method->getName(), '__')) {
                continue;
            }

            if ($method->isStatic()) {
                continue;
            }

            if ($method->getAttributes(Hidden::class) !== []) {
                continue;
            }

            $filters[Str::snake($method->getName())] = $method->getName();
        }

        return $filters;
    }
}
