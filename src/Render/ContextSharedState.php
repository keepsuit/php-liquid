<?php

namespace Keepsuit\Liquid\Render;

use Keepsuit\Liquid\Support\OutputsBag;
use WeakMap;

class ContextSharedState
{
    /**
     * @var WeakMap<object, mixed>
     */
    public WeakMap $computedObjectsCache;

    public function __construct(
        /** @var array<string, mixed> */
        public array $staticVariables = [],
        /** @var array<string, mixed> */
        public array $registers = [],
        /** @var array<\Throwable> */
        public array $errors = [],
        /** @var array<string, int> */
        public array $disabledTags = [],
        public OutputsBag $outputs = new OutputsBag,
    ) {
        $this->computedObjectsCache = new WeakMap;
    }
}
