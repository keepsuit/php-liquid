<?php

namespace Keepsuit\Liquid;

class Document
{
    public function __construct(
        protected readonly ParseContext $parseContext,
        protected BlockBody $body
    ) {
    }

    public static function parse(Tokenizer $tokenizer, ParseContext $parseContext): Document
    {
        return new Document(
            parseContext: $parseContext,
            body: BlockBody::fromSections(BlockParser::forDocument()->parse($tokenizer, $parseContext))
        );
    }

    public function nodeList(): array
    {
        return $this->body->nodeList();
    }
}
