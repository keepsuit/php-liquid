<?php

namespace Keepsuit\Liquid;

use Keepsuit\Liquid\Support\OutputsBag;
use Keepsuit\Liquid\Support\PartialsCache;

class TemplateSharedState
{
    public function __construct(
        /** @var array<\Throwable> */
        public array $errors = [],
        public PartialsCache $partialsCache = new PartialsCache,
        public OutputsBag $outputs = new OutputsBag,
    ) {}
}
