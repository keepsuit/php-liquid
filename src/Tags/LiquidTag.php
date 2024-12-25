<?php

namespace Keepsuit\Liquid\Tags;

use Keepsuit\Liquid\Exceptions\SyntaxException;
use Keepsuit\Liquid\Nodes\BodyNode;
use Keepsuit\Liquid\Parse\TagParseContext;
use Keepsuit\Liquid\Parse\Token;
use Keepsuit\Liquid\Parse\TokenType;
use Keepsuit\Liquid\Render\RenderContext;
use Keepsuit\Liquid\Tag;
use Keepsuit\Liquid\TagBlock;

class LiquidTag extends Tag
{
    protected BodyNode $body;

    public static function tagName(): string
    {
        return 'liquid';
    }

    public function parse(TagParseContext $context): static
    {
        $this->body = new BodyNode;

        while (! $context->params->isEnd()) {
            $this->body->pushChild($this->parseLine($context));
        }

        $context->params->assertEnd();

        return $this;
    }

    protected function parseLine(TagParseContext $context): Tag
    {
        $tokens = $context->params;

        $currentToken = $tokens->current();

        if ($currentToken === null) {
            throw SyntaxException::unexpectedEndOfTemplate();
        }

        $context->getParseContext()->lineNumber = $currentToken->lineNumber;

        $tagName = $tokens->consume(TokenType::Identifier)->data;

        /** @var class-string<Tag>|null $tagClass */
        $tagClass = $context->getParseContext()->environment->tagRegistry->get($tagName) ?? null;

        if ($tagClass === null || ! class_exists($tagClass)) {
            throw SyntaxException::unknownTag($tagName);
        }

        $tag = (new $tagClass)->setLineNumber($currentToken->lineNumber);

        if ($tag instanceof TagBlock) {
            $currentTagName = $tag::tagName();

            do {
                $params = $tokens->sliceUntil(fn (Token $token) => $token->lineNumber > $currentToken->lineNumber);

                $body = new BodyNode;

                $nextTag = $this->nextToken($context, $tag::tagName())->data;

                while ($nextTag !== $tag::blockDelimiter() && ! $tag->isSubTag($nextTag)) {
                    $body->pushChild($this->parseLine($context));

                    $nextTag = $this->nextToken($context, $tag::tagName())->data;
                }

                $tagParseContext = (new TagParseContext($currentTagName, $params, $body))
                    ->setParseContext($context->getParseContext());

                $tag->parse($tagParseContext);

                try {
                    $currentToken = $tokens->consume(TokenType::Identifier);
                    $currentTagName = $currentToken->data;
                } catch (SyntaxException $e) {
                    throw SyntaxException::tagBlockNeverClosed($tag::tagName());
                }
            } while ($currentTagName !== $tag::blockDelimiter());

            return $tag;
        }

        $params = $tokens->sliceUntil(fn (Token $token) => $token->lineNumber > $currentToken->lineNumber);

        $tagParseContext = (new TagParseContext($tagName, $params))
            ->setParseContext($context->getParseContext());

        $tag->parse($tagParseContext);

        return $tag;
    }

    public function render(RenderContext $context): string
    {
        return $this->body->render($context);
    }

    /**
     * @throws SyntaxException
     */
    protected function nextToken(TagParseContext $context, string $currentTag): Token
    {
        if (! $context->params->current()) {
            $context->getParseContext()->lineNumber++;

            throw SyntaxException::tagBlockNeverClosed($currentTag);
        }

        return $context->params->current();
    }
}
