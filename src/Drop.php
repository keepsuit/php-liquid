<?php

namespace Keepsuit\Liquid;

use Keepsuit\Liquid\Drops\DropMethodPrivate;

class Drop implements IsContextAware, MapsToLiquid
{
    protected ?Context $context = null;

    /**
     * @var string[]
     */
    private ?array $invokableMethods = null;

    public function setContext(Context $context): void
    {
        $this->context = $context;
    }

    public function toLiquid(): mixed
    {
        return $this;
    }

    public function toLiquidValue(): mixed
    {
        return $this;
    }

    protected function liquidMethodMissing(string $name): mixed
    {
        return null;
    }

    public function __toString(): string
    {
        return get_class($this);
    }

    public function __get(string $name): mixed
    {
        $invokableMethods = $this->getInvokableMethods();

        if ($invokableMethods === []) {
            return $this->liquidMethodMissing($name);
        }

        $possibleNames = function () use ($name) {
            yield $name;
            yield Str::camel($name);
            yield Str::snake($name);
        };

        foreach ($possibleNames() as $methodName) {
            if (! in_array($methodName, $invokableMethods)) {
                continue;
            }

            if (method_exists($this, $methodName)) {
                return $this->$methodName();
            }
        }

        return $this->liquidMethodMissing($name);
    }

    private function getInvokableMethods(): array
    {
        if ($this->invokableMethods !== null) {
            return $this->invokableMethods;
        }

        $blacklist = array_map(
            fn (\ReflectionMethod $method) => $method->getName(),
            (new \ReflectionClass(Drop::class))->getMethods(\ReflectionMethod::IS_PUBLIC)
        );

        $subClassPublicMethods = array_map(
            function (\ReflectionMethod $method) {
                if ($method->getAttributes(DropMethodPrivate::class) !== []) {
                    return null;
                }

                return $method->getName();
            },
            (new \ReflectionClass($this))->getMethods(\ReflectionMethod::IS_PUBLIC)
        );

        return $this->invokableMethods = array_filter(
            array_diff($subClassPublicMethods, $blacklist),
            fn (?string $name) => $name !== null && ! str_starts_with($name, '__')
        );
    }
}
