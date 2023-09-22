<?php

namespace Keepsuit\Liquid\Render;

use Keepsuit\Liquid\Template;
use WeakMap;

class ContextSharedState
{
    /**
     * @var array<string, Template>
     */
    public array $partialsCache = [];

    /**
     * @var WeakMap<object, mixed>
     */
    public WeakMap $computedObjectsCache;

    public function __construct(
        /** @var array<string, mixed> */
        public array $staticEnvironment = [],
        /** @var array<string, mixed> */
        public array $staticRegisters = [],
        /** @var array<\Throwable> */
        public array $errors = [],
        /** @var array<string, int> */
        public array $disabledTags = [],
    ) {
        $this->computedObjectsCache = new WeakMap();
    }
}
