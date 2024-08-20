<?php

namespace Keepsuit\Liquid\Parse;

use Closure;
use Keepsuit\Liquid\Contracts\LiquidFileSystem;
use Keepsuit\Liquid\Exceptions\ArithmeticException;
use Keepsuit\Liquid\Exceptions\InternalException;
use Keepsuit\Liquid\Exceptions\LiquidException;
use Keepsuit\Liquid\Exceptions\ResourceLimitException;
use Keepsuit\Liquid\Exceptions\StackLevelException;
use Keepsuit\Liquid\Exceptions\SyntaxException;
use Keepsuit\Liquid\FileSystems\BlankFileSystem;
use Keepsuit\Liquid\Nodes\BodyNode;
use Keepsuit\Liquid\Support\OutputsBag;
use Keepsuit\Liquid\Support\PartialsCache;
use Keepsuit\Liquid\Support\TagRegistry;
use Keepsuit\Liquid\Template;
use Throwable;

class ParseContext
{
    const MAX_DEPTH = 100;

    public int $lineNumber;

    public int $depth = 0;

    protected bool $partial = false;

    protected PartialsCache $partialsCache;

    protected OutputsBag $outputs;

    protected Lexer $lexer;

    protected Parser $parser;

    public function __construct(
        public readonly TagRegistry $tagRegistry = new TagRegistry,
        public readonly LiquidFileSystem $fileSystem = new BlankFileSystem,
    ) {
        $this->lineNumber = 1;
        $this->outputs = new OutputsBag;
        $this->partialsCache = new PartialsCache;
        $this->lexer = new Lexer($this);
        $this->parser = new Parser($this);
    }

    public function isPartial(): bool
    {
        return $this->partial;
    }

    /**
     * @throws SyntaxException
     */
    public function tokenize(string $markup): TokenStream
    {
        return $this->lexer->tokenize($markup);
    }

    public function parse(TokenStream $tokenStream): BodyNode
    {
        return $this->parser->parse($tokenStream);
    }

    public function loadPartial(string $templateName): Template
    {
        if ($cache = $this->partialsCache->get($templateName)) {
            return $cache;
        }

        $partialParseContext = new ParseContext(
            tagRegistry: $this->tagRegistry,
            fileSystem: $this->fileSystem,
        );
        $partialParseContext->partial = true;
        $partialParseContext->depth = $this->depth;
        $partialParseContext->outputs = $this->outputs;
        $partialParseContext->partialsCache = $this->partialsCache;

        try {
            $source = $this->fileSystem->readTemplateFile($templateName);

            $template = Template::parse($partialParseContext, $source, $templateName);

            $this->partialsCache->set($templateName, $template);

            return $template;
        } catch (LiquidException $exception) {
            $exception->templateName = $templateName;

            throw $exception;
        }
    }

    public function getPartialsCache(): PartialsCache
    {
        return $this->partialsCache;
    }

    public function getOutputs(): OutputsBag
    {
        return $this->outputs;
    }

    /**
     * @template TReturnValue
     *
     * @param  Closure(ParseContext): TReturnValue  $callback
     * @return TReturnValue
     *
     * @throws StackLevelException
     */
    public function nested(Closure $callback)
    {
        if ($this->depth >= self::MAX_DEPTH) {
            throw StackLevelException::nestingTooDeep();
        }

        $this->depth += 1;

        try {
            return $callback($this);
        } finally {
            $this->depth -= 1;
        }
    }

    /**
     * @throws LiquidException
     */
    public function handleError(Throwable $error): void
    {
        $error = match (true) {
            $error instanceof ResourceLimitException => throw $error,
            $error instanceof \ArithmeticError => new ArithmeticException($error),
            $error instanceof LiquidException => $error,
            default => new InternalException($error),
        };

        $error->lineNumber = $error->lineNumber ?? $this->lineNumber;

        throw $error;
    }
}
