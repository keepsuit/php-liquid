<?php

namespace Keepsuit\Liquid\Interrupts;

class Interrupt
{
    public function __construct(
        public readonly string $message = 'interrupt'
    ) {
    }
}
