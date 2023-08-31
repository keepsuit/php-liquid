<?php

namespace Keepsuit\Liquid;

use Keepsuit\Liquid\Exceptions\SyntaxException;
use Keepsuit\Liquid\Parse\ParseContext;
use Keepsuit\Liquid\Support\TagRegistry;

final class TemplateFactory
{
    public readonly TagRegistry $tagRegistry;

    public function __construct()
    {
        $this->tagRegistry = $this->buildTagRegistry();
    }

    public static function new(): TemplateFactory
    {
        return new self();
    }

    /**
     * @throws SyntaxException
     */
    public function parse(
        string $source,
        bool $lineNumbers = false,
    ): Template {
        $parseContext = new ParseContext(
            startLineNumber: $lineNumbers,
            tagRegistry: $this->tagRegistry
        );

        return Template::parse($parseContext, $source);
    }

    protected function buildTagRegistry(): TagRegistry
    {
        return (new TagRegistry())
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
            ->register(Tags\IncrementTag::class)
            ->register(Tags\InlineCommentTag::class)
            ->register(Tags\LiquidTag::class)
            ->register(Tags\RawTag::class)
            ->register(Tags\RenderTag::class)
            ->register(Tags\TableRowTag::class)
            ->register(Tags\UnlessTag::class);
    }

    /**
     * @param  class-string<Tag>  $tag
     */
    public function registerTag(string $tag): TemplateFactory
    {
        $this->tagRegistry->register($tag);

        return $this;
    }
}
