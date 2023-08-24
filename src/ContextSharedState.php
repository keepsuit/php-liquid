<?php

namespace Keepsuit\Liquid;

class ContextSharedState
{
    public function __construct(
        /** @var array<\Throwable> */
        public array $errors = [],
        /** @var array<string, mixed> */
        public array $registers = [],
        /** @var array<class-string> $filters */
        public array $filters = [],
    ) {
    }
}
