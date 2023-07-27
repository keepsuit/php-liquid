<?php

namespace Keepsuit\Liquid;

class Document
{
    protected BlockBody $body;

    public function __construct(
        protected readonly ParseContext $parseContext,
        BlockBody $body = null
    ) {
        $this->body = $body ?? $this->parseContext->newBlockBody();
    }

    public static function parse(Tokenizer $tokenizer, ParseContext $parseContext): Document
    {
        return new Document(
            parseContext: $parseContext,
            body: BlockBody::parse($tokenizer, $parseContext)
        );
    }

    /**
     * @return array<Tag|Variable|string>
     */
    public function nodeList(): array
    {
        return $this->body->nodeList();
    }
}
