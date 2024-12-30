<?php

namespace Keepsuit\Liquid\Render;

use Keepsuit\Liquid\Support\OutputsBag;
use Keepsuit\Liquid\Support\PartialsCache;
use WeakMap;

class ContextSharedState
{
    public readonly PartialsCache $partialsCache;

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
        $this->partialsCache = new PartialsCache;
        $this->computedObjectsCache = new WeakMap;
    }
}
