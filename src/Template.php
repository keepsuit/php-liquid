<?php

namespace Keepsuit\Liquid;

use Keepsuit\Liquid\Exceptions\SyntaxException;
use Keepsuit\Liquid\Nodes\Document;
use Keepsuit\Liquid\Parse\ParseContext;
use Keepsuit\Liquid\Profiler\Profiler;
use Keepsuit\Liquid\Render\Context;
use Keepsuit\Liquid\Support\TagRegistry;

class Template
{
    protected static TagRegistry $tagRegistry;

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
    public static function parse(
        string $source,
        bool $lineNumbers = false,
    ): Template {
        $parseContext = new ParseContext();
        $tokenizer = $parseContext->newTokenizer($source, startLineNumber: $lineNumbers ? 1 : null);

        return new Template(
            root: Document::parse($tokenizer, $parseContext)
        );
    }

    public static function parsePartial(string $source, ParseContext $parseContext, string $name = null): Template
    {
        $tokenizer = $parseContext->newTokenizer($source, startLineNumber: $parseContext->lineNumber !== null ? 1 : null);

        return new Template(
            root: Document::parse($tokenizer, $parseContext),
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

    /**
     * @return array<string, class-string<Tag>>
     */
    public static function registeredTags(): array
    {
        return static::getTagRegistry()->all();
    }

    /**
     * @param  class-string<Tag>  $tag
     */
    public static function registerTag(string $tag): void
    {
        static::getTagRegistry()->register($tag);
    }

    protected static function getTagRegistry(): TagRegistry
    {
        if (isset(static::$tagRegistry)) {
            return static::$tagRegistry;
        }

        return static::$tagRegistry = (new TagRegistry())
            ->register(Tags\AssignTag::class)
            ->register(Tags\BreakTag::class)
            ->register(Tags\CaptureTag::class)
            ->register(Tags\CaseTag::class)
            ->register(Tags\CommentTag::class)
            ->register(Tags\ContinueTag::class)
            ->register(Tags\CycleTag::class)
            ->register(Tags\DecrementTag::class)
            ->register(Tags\EchoTag::class)
            ->register(Tags\ForTag::class)
            ->register(Tags\IfChanged::class)
            ->register(Tags\IfTag::class)
            ->register(Tags\IncludeTag::class)
            ->register(Tags\IncrementTag::class)
            ->register(Tags\InlineCommentTag::class)
            ->register(Tags\LiquidTag::class)
            ->register(Tags\RawTag::class)
            ->register(Tags\RenderTag::class)
            ->register(Tags\TableRowTag::class)
            ->register(Tags\UnlessTag::class);
    }

    public static function deleteTag(string $name): void
    {
        static::getTagRegistry()->delete($name);
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
