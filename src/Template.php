<?php

namespace Keepsuit\Liquid;

use Keepsuit\Liquid\Exceptions\SyntaxException;
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
    ) {
    }

    /**
     * @throws SyntaxException
     */
    public static function parse(ParseContext $parseContext, string $source, string $name = null): Template
    {
        $tokenizer = $parseContext->newTokenizer($source);

        return new Template(
            root: Document::parse($parseContext, $tokenizer),
            name: $name,
        );
    }

    public function render(Context $context): string
    {
        $this->profiler = $context->getProfiler();

        try {
            return $this->root->render($context);
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
}
