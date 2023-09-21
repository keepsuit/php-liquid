<?php

namespace Keepsuit\Liquid\Support;

trait GeneratorToString
{
    /**
     * @param  \Generator<string>  $generator
     * @return string
     */
    protected function generatorToString(\Generator $generator): string
    {
        return implode('', iterator_to_array($generator, false));
    }
}
