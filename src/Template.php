<?php

namespace Keepsuit\Liquid;

use Keepsuit\Liquid\Exceptions\LiquidException;
use Keepsuit\Liquid\Nodes\Document;
use Keepsuit\Liquid\Parse\ParseContext;
use Keepsuit\Liquid\Profiler\Profiler;
use Keepsuit\Liquid\Render\Context;

class Template
{
    /**
     * @var array<\Throwable>
     */
    protected array $errors = [];

    protected ?Profiler $profiler = null;

    public function __construct(
        public readonly Document $root,
        public readonly ?string $name = null,
        /** @var array<string,Template> */
        protected array $partialsCache = [],
    ) {
    }

    /**
     * @throws LiquidException
     */
    public static function parse(ParseContext $parseContext, string $source, string $name = null): Template
    {
        try {
            $tokenizer = $parseContext->newTokenizer($source);
            $root = Document::parse($parseContext, $tokenizer);

            return new Template(
                root: $root,
                name: $name,
                partialsCache: $parseContext->isPartial() ? [] : $parseContext->getPartialsCache(),
            );
        } catch (LiquidException $e) {
            $e->templateName = $e->templateName ?? $name;
            throw $e;
        }
    }

    /**
     * @throws LiquidException
     */
    public function render(Context $context): string
    {
        $this->profiler = $context->getProfiler();

        try {
            $context->mergePartialsCache($this->partialsCache);

            return $this->root->render($context);
        } catch (LiquidException $e) {
            $e->templateName = $e->templateName ?? $this->name;
            throw $e;
        } finally {
            $this->errors = $context->getErrors();
        }
    }

    public function getErrors(): array
    {
        return $this->errors;
    }

    public function getProfiler(): ?Profiler
    {
        return $this->profiler;
    }

    /**
     * @return array<string,Template>
     */
    public function getPartialsCache(): array
    {
        return $this->partialsCache;
    }
}
