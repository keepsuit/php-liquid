<?php

namespace Keepsuit\Liquid;

use Keepsuit\Liquid\Concerns\ContextAware;
use Keepsuit\Liquid\Contracts\IsContextAware;
use Keepsuit\Liquid\Exceptions\UndefinedDropMethodException;
use Keepsuit\Liquid\Support\DropMetadata;
use Keepsuit\Liquid\Support\Str;

class Drop implements IsContextAware
{
    use ContextAware;

    private ?DropMetadata $metadata = null;

    private array $cache = [];

    protected function liquidMethodMissing(string $name): mixed
    {
        throw new UndefinedDropMethodException($name);
    }

    public function toArray(): array
    {
        $result = [];

        foreach ($this->getMetadata()->properties as $property) {
            $result[$property] = $this->{$property};
        }

        foreach ($this->getMetadata()->dynamicProperties as $property) {
            $result[$property] = $this->{$property};
        }

        foreach ($this->getMetadata()->invokableMethods as $method) {
            $result[$method] = $this->{$method};
        }

        return $result;
    }

    public function __toString(): string
    {
        return get_class($this);
    }

    public function __get(string $name): mixed
    {
        $invokableMethods = $this->getMetadata()->invokableMethods;
        $cacheableMethods = $this->getMetadata()->cacheableMethods;

        $possibleNames = array_unique([
            $name,
            Str::camel($name),
            Str::snake($name),
        ]);

        foreach ($possibleNames as $propertyName) {
            if (in_array($propertyName, $this->getMetadata()->properties)) {
                return $this->{$propertyName};
            }
        }

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

        if (isset($this->context) && $this->context->options->strictVariables) {
            throw new UndefinedDropMethodException($name);
        }

        return null;
    }

    protected function getMetadata(): DropMetadata
    {
        return $this->metadata ??= DropMetadata::init($this);
    }
}
