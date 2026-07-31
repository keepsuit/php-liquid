<?php

namespace Keepsuit\Liquid\Compiler;

use Keepsuit\Liquid\Nodes\Document;
use Keepsuit\Liquid\Template;
use Keepsuit\Liquid\TemplateSharedState;

class CompiledTemplate extends Template implements CompiledTemplateInterface
{
    public function __construct(Document $root, ?TemplateSharedState $state = null)
    {
        parent::__construct($root, $state ?? new TemplateSharedState);
    }
}
