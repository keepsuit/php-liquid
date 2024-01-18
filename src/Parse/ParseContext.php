<?php

namespace Keepsuit\Liquid\Parse;

use Closure;
use Keepsuit\Liquid\Contracts\LiquidFileSystem;
use Keepsuit\Liquid\Exceptions\ArithmeticException;
use Keepsuit\Liquid\Exceptions\InternalException;
use Keepsuit\Liquid\Exceptions\LiquidException;
use Keepsuit\Liquid\Exceptions\ResourceLimitException;
use Keepsuit\Liquid\Exceptions\StackLevelException;
use Keepsuit\Liquid\FileSystems\BlankFileSystem;
use Keepsuit\Liquid\Support\Arr;
use Keepsuit\Liquid\Support\I18n;
use Keepsuit\Liquid\Support\OutputsBag;
use Keepsuit\Liquid\Support\TagRegistry;
use Keepsuit\Liquid\Template;
use Throwable;

class ParseContext
{
    const MAX_DEPTH = 100;

    public ?int $lineNumber = null;

    public bool $trimWhitespace = false;

    public int $depth = 0;

    protected bool $partial = false;

    /**
     * @var array<string,Template>
     */
    protected array $partialsCache = [];

    protected OutputsBag $outputs;

    public function __construct(
        bool|int|null $startLineNumber = null,
        public readonly TagRegistry $tagRegistry = new TagRegistry(),
        public readonly LiquidFileSystem $fileSystem = new BlankFileSystem(),
        public readonly I18n $locale = new I18n(),
    ) {
        $this->lineNumber = match (true) {
            is_int($startLineNumber) => $startLineNumber,
            $startLineNumber === true => 1,
            default => null,
        };

        $this->outputs = new OutputsBag();
    }

    public function isPartial(): bool
    {
        return $this->partial;
    }

    public function newTokenizer(string $markup, bool $forLiquidTag = false): Tokenizer
    {
        return new Tokenizer($markup, startLineNumber: $this->lineNumber, forLiquidTag: $forLiquidTag);
    }

    public function parseExpression(string $markup): mixed
    {
        return Expression::parse($markup);
    }

    public function loadPartial(string $templateName): Template
    {
        if (Arr::has($this->partialsCache, $templateName)) {
            return $this->partialsCache[$templateName];
        }

        $oldLineNumber = $this->lineNumber;
        $this->partial = true;
        $this->lineNumber = $this->lineNumber !== null ? 1 : null;

        try {
            $source = $this->fileSystem->readTemplateFile($templateName);
            $template = Template::parse($this, $source, $templateName);
            $this->partialsCache[$templateName] = $template;

            return $template;
        } catch (LiquidException $exception) {
            $exception->templateName = $templateName;

            throw $exception;
        } finally {
            $this->partial = false;
            $this->lineNumber = $oldLineNumber;
        }
    }

    public function getPartialsCache(): array
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
            throw new StackLevelException($this->locale->translate('errors.stack.nesting_too_deep'));
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

        $error->lineNumber = $this->lineNumber;

        throw $error;
    }
}
