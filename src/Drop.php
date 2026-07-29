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
            $result[Str::snake($property)] = $this->{$property};
        }

        foreach ($this->getMetadata()->dynamicProperties as $property) {
            $result[Str::snake($property)] = $this->{$property};
        }

        foreach ($this->getMetadata()->invokableMethods as $method) {
            $result[Str::snake($method)] = $this->{$method};
        }

        return $result;
    }

    public function __toString(): string
    {
        return get_class($this);
    }

    public function __get(string $name): mixed
    {
        $metadata = $this->getMetadata();
        $resolution = $metadata->resolveStatic($name);

        if ($resolution !== null) {
            $memberName = $resolution['name'];

            if ($resolution['type'] === 'property') {
                return $this->{$memberName};
            }

            if ($resolution['cacheable'] && array_key_exists($memberName, $this->cache)) {
                return $this->cache[$memberName];
            }

            $result = $this->{$memberName}();

            if ($resolution['cacheable']) {
                $this->cache[$memberName] = $result;
            }

            return $result;
        }

        foreach ($metadata->possibleNames($name) as $methodName) {
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
