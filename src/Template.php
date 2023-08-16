<?php

namespace Keepsuit\Liquid;

class Template
{
    protected static TagRegistry $tagRegistry;

    public static ErrorMode $errorMode = ErrorMode::Warn;

    public function __construct(
        public readonly Document $root
    ) {
    }

    public static function parse(
        string $source,
        bool $lineNumbers = false,
        ErrorMode $errorMode = null,
    ): Template {
        $parseContext = new ParseContext(errorMode: $errorMode ?? static::$errorMode);
        $tokenizer = $parseContext->newTokenizer($source, startLineNumber: $lineNumbers ? 1 : null);

        return new Template(
            root: Document::parse($tokenizer, $parseContext)
        );
    }

    public function render(Context $context): string
    {
        return $this->root->render($context);
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
            ->register(Tags\CaptureTag::class)
            ->register(Tags\CaseTag::class)
            ->register(Tags\CommentTag::class)
            ->register(Tags\CycleTag::class)
            ->register(Tags\EchoTag::class)
            ->register(Tags\ForTag::class)
            ->register(Tags\IfTag::class)
            ->register(Tags\IncludeTag::class)
            ->register(Tags\RenderTag::class)
            ->register(Tags\TableRowTag::class)
            ->register(Tags\UnlessTag::class);
    }

    public static function deleteTag(string $name): void
    {
        static::getTagRegistry()->delete($name);
    }
}
