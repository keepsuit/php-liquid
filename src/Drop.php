<?php

namespace Keepsuit\Liquid;

use Keepsuit\Liquid\Concerns\ContextAware;
use Keepsuit\Liquid\Contracts\IsContextAware;
use Keepsuit\Liquid\Drops\DropMethodPrivate;
use Keepsuit\Liquid\Exceptions\UndefinedDropMethodException;
use Keepsuit\Liquid\Support\Str;

class Drop implements IsContextAware
{
    use ContextAware;

    /**
     * @var string[]
     */
    private ?array $invokableMethods = null;

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

        $possibleNames = [
            $name,
            Str::camel($name),
            Str::snake($name),
        ];

        foreach ($possibleNames as $methodName) {
            if (! in_array($methodName, $invokableMethods)) {
                continue;
            }

            if (method_exists($this, $methodName)) {
                return $this->{$methodName}();
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
        if ($this->invokableMethods !== null) {
            return $this->invokableMethods;
        }

        $blacklist = array_map(
            fn (\ReflectionMethod $method) => $method->getName(),
            (new \ReflectionClass(Drop::class))->getMethods(\ReflectionMethod::IS_PUBLIC)
        );

        if ($this instanceof \Traversable) {
            $blacklist = [...$blacklist, 'current', 'next', 'key', 'valid', 'rewind'];
        }

        $subClassPublicMethods = array_map(
            function (\ReflectionMethod $method) {
                if ($method->getAttributes(DropMethodPrivate::class) !== []) {
                    return null;
                }

                return $method->getName();
            },
            (new \ReflectionClass($this))->getMethods(\ReflectionMethod::IS_PUBLIC)
        );

        return $this->invokableMethods = array_values(array_filter(
            ['toLiquid', ...array_diff($subClassPublicMethods, $blacklist)],
            fn (?string $name) => $name !== null && ! str_starts_with($name, '__')
        ));
    }
}
