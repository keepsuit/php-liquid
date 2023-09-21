<?php

namespace Keepsuit\Liquid\Support;

trait GeneratorToString
{
    /**
     * @param  \Generator<string>  $generator
     */
    protected function generatorToString(\Generator $generator): string
    {
        return implode('', iterator_to_array($generator, false));
    }
}
