<?php

namespace Keepsuit\Liquid\Parse;

use Closure;
use Keepsuit\Liquid\Environment;
use Keepsuit\Liquid\Exceptions\InternalException;
use Keepsuit\Liquid\Exceptions\LiquidException;
use Keepsuit\Liquid\Exceptions\StackLevelException;
use Keepsuit\Liquid\Exceptions\SyntaxException;
use Keepsuit\Liquid\Support\OutputsBag;
use Keepsuit\Liquid\Template;
use Keepsuit\Liquid\TemplateSharedState;

class ParseContext
{
    const MAX_DEPTH = 100;

    public int $lineNumber;

    public int $depth = 0;

    protected bool $partial = false;

    /**
     * @var string[]
     */
    protected array $partials = [];

    protected OutputsBag $outputs;

    protected Lexer $lexer;

    protected Parser $parser;

    public readonly Environment $environment;

    public function __construct(
        ?Environment $environment = null,
    ) {
        $this->environment = $environment ?? Environment::default();

        $this->lineNumber = 1;
        $this->outputs = new OutputsBag;
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

    /**
     * @throws LiquidException
     */
    public function parseTemplate(string $templateName, bool $force = false): Template
    {
        $cachedTemplate = $this->environment->templatesCache->get($templateName);

        if ($cachedTemplate !== null && ! $force) {
            return $cachedTemplate;
        }

        $source = $this->environment->fileSystem->readTemplateFile($templateName);

        $template = $this->parse($source, name: $templateName);

        $this->environment->templatesCache->set($templateName, $template);

        return $template;
    }

    public function parse(TokenStream|string $source, ?string $name = null): Template
    {
        $this->partials = [];
        $this->outputs = new OutputsBag;

        try {
            $tokenStream = $source instanceof TokenStream ? $source : $this->tokenize($source);

            $root = $this->parser->parse($tokenStream, $name);

            return new Template(
                root: $root,
                state: new TemplateSharedState(
                    partials: $this->partials,
                    outputs: (clone $this->outputs),
                ),
            );
        } catch (LiquidException $e) {
            $e->templateName = $e->templateName ?? $name;
            $e->lineNumber = $e->lineNumber ?? $this->lineNumber;
            throw $e;
        } catch (\Throwable $e) {
            $exception = new InternalException($e);
            $exception->templateName = $exception->templateName ?? $name;
            $exception->lineNumber = $exception->lineNumber ?? $this->lineNumber;
            throw $exception;
        }
    }

    public function loadPartial(string $templateName): Template
    {
        $partialParseContext = new ParseContext(environment: $this->environment);
        $partialParseContext->partial = true;
        $partialParseContext->depth = $this->depth;

        try {
            $template = $partialParseContext->parseTemplate($templateName);

            $this->outputs->merge($partialParseContext->outputs);

            if (! in_array($templateName, $this->partials, true)) {
                $this->partials[] = $templateName;
            }

            return $template;
        } catch (LiquidException $exception) {
            $exception->templateName = $templateName;

            throw $exception;
        }
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
}
