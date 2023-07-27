<?php

namespace Keepsuit\Liquid;

use Keepsuit\Liquid\Tags\Comment;

class Template
{
    protected static TagRegistry $tagRegistry;

    public function __construct(
        public readonly Document $root
    ) {
    }

    public static function parse(string $source, array $options = []): Template
    {
        $parseContext = new ParseContext($options);
        $tokenizer = $parseContext->newTokenizer($source, startLineNumber: ($options['line_numbers'] ?? false) ? 1 : null);

        return new Template(
            root: Document::parse($tokenizer, $parseContext)
        );
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
            ->register(Comment::class);
    }

    public static function deleteTag(string $name): void
    {
        static::getTagRegistry()->delete($name);
    }
}
