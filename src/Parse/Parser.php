<?php

namespace Keepsuit\Liquid\Parse;

use Keepsuit\Liquid\Exceptions\StandardException;
use Keepsuit\Liquid\Exceptions\SyntaxException;
use Keepsuit\Liquid\Nodes\BodyNode;
use Keepsuit\Liquid\Nodes\Document;
use Keepsuit\Liquid\Nodes\Raw;
use Keepsuit\Liquid\Nodes\Text;
use Keepsuit\Liquid\Nodes\Variable;
use Keepsuit\Liquid\Tag;
use Keepsuit\Liquid\TagBlock;

class Parser
{
    protected TokenStream $tokenStream;

    /**
     * @var TagBlock[]
     */
    protected array $blockScopes;

    public function __construct(
        protected ParseContext $parseContext,
    ) {}

    /**
     * @throws SyntaxException
     * @throws StandardException
     */
    public function parse(TokenStream $tokenStream): Document
    {
        $this->tokenStream = $tokenStream;
        $this->blockScopes = [];

        $body = $this->subparse();
        $document = new Document($body);

        return $this->newNodeTraverser()->traverse($document);
    }

    /**
     * @throws SyntaxException
     */
    protected function subparse(): BodyNode
    {
        if ($this->currentToken() === null) {
            return new BodyNode([]);
        }

        $nodes = [];

        while (! $this->tokenStream->isEnd()) {
            $token = $this->tokenStream->next();
            $this->parseContext->lineNumber = $token->lineNumber;

            switch ($token->type) {
                case TokenType::TextData:
                    $nodes[] = (new Text($token->data))->setLineNumber($this->parseContext->lineNumber);
                    break;
                case TokenType::RawData:
                    $nodes[] = (new Raw($token->data))->setLineNumber($this->parseContext->lineNumber);
                    break;
                case TokenType::VariableStart:
                    $nodes[] = $this->parseVariable();

                    break;
                case TokenType::BlockStart:
                    try {
                        $tagName = $this->tokenStream->consume(TokenType::Identifier)->data;
                        $this->tokenStream->jump(-1);
                    } catch (SyntaxException $e) {
                        throw new SyntaxException('A block must start with a tag name.');
                    }

                    if ($this->isEndOrSubTagOfCurrentBlock($tagName)) {
                        return new BodyNode($nodes);
                    }

                    $nodes[] = $this->parseBlock();
                    break;
                default:
                    throw new SyntaxException('Unexpected token type: '.$token->type->toString());
            }
        }

        return new BodyNode($nodes);
    }

    protected function parseVariable(): Variable
    {
        $variable = $this->tokenStream->variable();

        $this->tokenStream->consume(TokenType::VariableEnd);

        return $variable;
    }

    /**
     * @throws SyntaxException
     */
    protected function parseBlock(): Tag
    {
        $currentToken = $this->tokenStream->current();

        $tagName = $this->tokenStream->consume(TokenType::Identifier)->data;

        /** @var class-string<Tag>|null $tagClass */
        $tagClass = $this->parseContext->environment->tagRegistry->get($tagName) ?? null;

        if ($tagClass === null || ! class_exists($tagClass)) {
            $blockTagName = $this->currentBlockScope() ? $this->currentBlockScope()::tagName() : null;

            throw SyntaxException::unknownTag($tagName, $blockTagName);
        }

        $tag = (new $tagClass)->setLineNumber($currentToken?->lineNumber);

        if ($tag instanceof TagBlock) {
            $this->blockScopes[] = $tag;

            $currentTagName = $tagName;
            do {
                $params = $this->tokenStream->sliceUntil(TokenType::BlockEnd);
                $this->tokenStream->consume(TokenType::BlockEnd);

                $body = $this->subparse();
                $tagParseContext = (new TagParseContext($currentTagName, $params, $body))->setParseContext($this->parseContext);

                $tag->parse($tagParseContext);

                try {
                    $currentTagName = $this->tokenStream->consume(TokenType::Identifier)->data;
                } catch (SyntaxException $e) {
                    throw SyntaxException::tagBlockNeverClosed($tag::tagName());
                }
            } while ($currentTagName !== $tag::blockDelimiter());

            $this->tokenStream->consume(TokenType::BlockEnd);

            array_pop($this->blockScopes);

            return $tag;
        }

        $params = $this->tokenStream->sliceUntil(TokenType::BlockEnd);
        $this->tokenStream->consume(TokenType::BlockEnd);

        $tagParseContext = (new TagParseContext($tagName, $params))
            ->setParseContext($this->parseContext);

        $tag->parse($tagParseContext);

        return $tag;
    }

    protected function currentBlockScope(): ?TagBlock
    {
        return $this->blockScopes[count($this->blockScopes) - 1] ?? null;
    }

    protected function isEndOrSubTagOfCurrentBlock(string $tagName): bool
    {
        $currentBlock = $this->currentBlockScope();

        if (! $currentBlock) {
            return false;
        }

        if ($tagName === $currentBlock::blockDelimiter()) {
            return true;
        }

        return $currentBlock->isSubTag($tagName);
    }

    public function getParseContext(): ParseContext
    {
        return $this->parseContext;
    }

    public function getTokenStream(): TokenStream
    {
        return $this->tokenStream;
    }

    public function currentToken(): ?Token
    {
        return $this->tokenStream->current();
    }

    protected function newNodeTraverser(): NodeTraverser
    {
        return new NodeTraverser(visitors: $this->parseContext->environment->getExtensionNodeVisitors());
    }
}
