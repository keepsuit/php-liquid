<?php

namespace Keepsuit\Liquid;

use Keepsuit\Liquid\Concerns\ContextAware;
use Keepsuit\Liquid\Contracts\IsContextAware;
use Keepsuit\Liquid\Drops\Cache;
use Keepsuit\Liquid\Drops\Hidden;
use Keepsuit\Liquid\Exceptions\UndefinedDropMethodException;
use Keepsuit\Liquid\Support\Str;

class Drop implements IsContextAware
{
    use ContextAware;

    /**
     * @var string[]
     */
    private ?array $invokableMethods = null;

    /**
     * @var string[]
     */
    private ?array $cacheableMethods = null;

    private array $cache = [];

    protected function liquidMethodMissing(string $name): mixed
    {
        throw new UndefinedDropMethodException($name);
    }

    public function __toString(): string
    {
        return get_class($this);
    }

    public function __get(string $name): mixed
    {
        $invokableMethods = $this->getInvokableMethods();
        $cacheableMethods = $this->getCacheableMethods();

        $possibleNames = [
            $name,
            Str::camel($name),
            Str::snake($name),
        ];

        foreach ($possibleNames as $methodName) {
            if (! in_array($methodName, $invokableMethods)) {
                continue;
            }

            $isCacheable = in_array($methodName, $cacheableMethods);

            if ($isCacheable && isset($this->cache[$methodName])) {
                return $this->cache[$methodName];
            }

            if (method_exists($this, $methodName)) {
                $result = $this->{$methodName}();

                if ($isCacheable) {
                    $this->cache[$methodName] = $result;
                }

                return $result;
            }
        }

        foreach ($possibleNames as $methodName) {
            try {
                return $this->liquidMethodMissing($methodName);
            } catch (UndefinedDropMethodException) {
            }
        }

        if ($this->context->strictVariables) {
            throw new UndefinedDropMethodException($name);
        }

        return null;
    }

    protected function getInvokableMethods(): array
    {
        if ($this->invokableMethods === null) {
            $this->init();
        }

        return $this->invokableMethods;
    }

    protected function getCacheableMethods(): array
    {
        if ($this->cacheableMethods === null) {
            $this->init();
        }

        return $this->cacheableMethods;
    }

    protected function init(): void
    {
        $blacklist = array_map(
            fn (\ReflectionMethod $method) => $method->getName(),
            (new \ReflectionClass(Drop::class))->getMethods(\ReflectionMethod::IS_PUBLIC)
        );

        if ($this instanceof \Traversable) {
            $blacklist = [...$blacklist, 'current', 'next', 'key', 'valid', 'rewind'];
        }

        $publicMethods = (new \ReflectionClass($this))->getMethods(\ReflectionMethod::IS_PUBLIC);

        $visibleMethodNames = array_map(
            fn (\ReflectionMethod $method) => $method->getAttributes(Hidden::class) !== [] ? null : $method->getName(),
            $publicMethods
        );

        $this->invokableMethods = array_values(array_filter(
            array_diff($visibleMethodNames, $blacklist),
            fn (?string $name) => $name !== null && ! str_starts_with($name, '__')
        ));

        $this->cacheableMethods = array_values(array_filter(array_map(
            fn (\ReflectionMethod $method) => $method->getAttributes(Cache::class) !== [] ? $method->getName() : null,
            $publicMethods
        )));
    }
}
