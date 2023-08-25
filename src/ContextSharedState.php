<?php

namespace Keepsuit\Liquid;

use Closure;
use WeakMap;

class ContextSharedState
{
    /**
     * @var array<string, Template>
     */
    public array $partialsCache = [];

    /**
     * @var WeakMap<Closure, mixed>
     */
    public WeakMap $closuresCache;

    public function __construct(
        /** @var array<string, mixed> */
        public array $staticEnvironment = [],
        /** @var array<string, mixed> */
        public array $staticRegisters = [],
        /** @var array<\Throwable> */
        public array $errors = [],
        /** @var array<class-string> $filters */
        public array $filters = [],
    ) {
        $this->closuresCache = new WeakMap();
    }
}
