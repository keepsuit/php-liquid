<?php

namespace Keepsuit\Liquid\Nodes;

use Keepsuit\Liquid\Parse\ParseContext;
use Keepsuit\Liquid\Parse\TokenStream;

class TagParseContext
{
    protected ParseContext $parseContext;

    public function __construct(
        public readonly string $tag,
        public readonly TokenStream $params,
        public readonly ?BodyNode $body = null,
    ) {
    }

    public function setParseContext(ParseContext $parseContext): static
    {
        $this->parseContext = $parseContext;

        return $this;
    }

    public function getParseContext(): ParseContext
    {
        return $this->parseContext;
    }
}
