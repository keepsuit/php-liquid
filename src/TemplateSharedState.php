<?php

namespace Keepsuit\Liquid;

use Keepsuit\Liquid\Support\OutputsBag;

class TemplateSharedState
{
    public function __construct(
        /** @var array<\Throwable> */
        public array $errors = [],
        /** @var string[] */
        public array $partials = [],
        public OutputsBag $outputs = new OutputsBag,
    ) {}
}
