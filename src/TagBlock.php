<?php

namespace Keepsuit\Liquid;

use Keepsuit\Liquid\Contracts\HasParseTreeVisitorChildren;
use Keepsuit\Liquid\Exceptions\SyntaxException;
use Keepsuit\Liquid\Nodes\BodyNode;
use Keepsuit\Liquid\Nodes\TagParseContext;
use Keepsuit\Liquid\Parse\Parser;
use Keepsuit\Liquid\Parse\TokenStream;
use Keepsuit\Liquid\Parse\TokenType;

abstract class TagBlock extends Tag implements HasParseTreeVisitorChildren
{
    //    /**
    //     * @var array<TagParseContext>
    //     */
    //    protected array $bodySections = [];
    //
    //    public function parse(TagParseContext $context): static
    //    {
    //        $this->bodySections[] = $context;
    ////        $this->bodySections = [];
    ////        $tag = static::tagName();
    ////
    ////        do {
    ////            $params = $this->parseParams($parser);
    ////            $body = $this->parseBody($parser);
    ////            $this->bodySections[] = new TagBodySection($tag, $params, $body);
    ////
    ////            $tag = $parser->getTokenStream()->consume(TokenType::Identifier)->data;
    ////        } while ($tag !== static::blockDelimiter());
    ////
    ////        $parser->getTokenStream()->consume(TokenType::BlockEnd);
    //
    //        return $this;
    //    }

    //    private function parseParams(Parser $parser): TokenStream
    //    {
    //        $params = [];
    //
    //        while ($parser->getTokenStream()->current()?->type !== TokenType::BlockEnd) {
    //            $params[] = $parser->getTokenStream()->consume();
    //        }
    //
    //        return new TokenStream($params);
    //    }
    //
    //    private function parseBody(Parser $parser): BodyNode
    //    {
    //        if ($parser->getTokenStream()->current()?->type !== TokenType::BlockEnd) {
    //            throw new SyntaxException('Cannot parse block body: block start tag not parsed');
    //        }
    //
    //        $parser->getTokenStream()->consume(TokenType::BlockEnd);
    //
    //        return $parser->subparse();
    //    }

    //    protected function parseBody(Parser $parser): BodyNode
    //    {
    ////        if ($this->bodySections !== []) {
    ////            return $this->bodySections;
    ////        }
    //
    ////        $tokenStream = $parser->getTokenStream();
    //
    //        return $parser->subparse();
    //
    ////        do {
    ////            $nodes = $parser->subparse();
    ////            $endTag = $tokenStream->consume(TokenType::Identifier)->value;
    ////
    //////            $this->bodySections[] = new BlockBodySection(
    //////                start: $startTag,
    //////                end: $endTag,
    //////                nodeList: $nodes,
    //////            );
    ////
    ////            $startTag = $endTag;
    ////
    //////            $tokenStream->consume(TokenType::BlockEnd);
    ////        } while ($endTag !== static::blockDelimiter());
    ////
    ////        return $this->bodySections;
    //    }

    //    public function blank(): bool
    //    {
    //        foreach ($this->bodySections as $bodySection) {
    //            if (! $bodySection->blank()) {
    //                return false;
    //            }
    //        }
    //
    //        return true;
    //    }

    //    public function children(): array
    //    {
    //        return $this->bodySections;
    //    }

    //    public function render(Context $context): string
    //    {
    //        return $context->withDisabledTags($this->disabledTags(), function (Context $context) {
    //            $output = '';
    //
    //            foreach ($this->bodySections as $bodySection) {
    //                $output .= $bodySection->render($context);
    //            }
    //
    //            return $output;
    //        });
    //    }

    public function isSubTag(string $tagName): bool
    {
        return false;
    }

    public static function blockDelimiter(): string
    {
        return 'end'.static::tagName();
    }

    public function parseTreeVisitorChildren(): array
    {
        return [];
    }
}
