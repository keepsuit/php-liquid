<?php

namespace Keepsuit\Liquid;

class Document
{
    public function __construct(
        protected readonly ParseContext $parseContext,
        protected BlockBodySection $body,
    ) {
    }

    public static function parse(Tokenizer $tokenizer, ParseContext $parseContext): Document
    {
        return new Document(
            parseContext: $parseContext,
            body: BlockParser::forDocument()->parse($tokenizer, $parseContext)[0] ?? new BlockBodySection()
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
