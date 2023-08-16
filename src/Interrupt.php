<?php

namespace Keepsuit\Liquid;

class Interrupt
{
    public function __construct(
        public readonly string $message = 'interrupt'
    ) {
    }
}
