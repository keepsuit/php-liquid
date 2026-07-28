<?php

namespace Keepsuit\Liquid\Support;

use Keepsuit\Liquid\Tag;
use Keepsuit\Liquid\TagBlock;

class TagRegistry
{
    /**
     * @var array<string, class-string<Tag>>
     */
    protected array $tags = [];

    /**
     * Lazily derived from $tags. The lexer needs this on every tokenize() call, and
     * deriving it walks every registered tag, so cache it until the registry changes.
     *
     * @var array<string, true>|null
     */
    protected ?array $rawBodyTags = null;

    /**
     * @param  class-string<Tag>  $tag
     * @return $this
     */
    public function register(string $tag): static
    {
        $this->tags[$tag::tagName()] = $tag;
        $this->rawBodyTags = null;

        return $this;
    }

    public function delete(string $name): static
    {
        unset($this->tags[$name]);
        $this->rawBodyTags = null;

        return $this;
    }

    /**
     * Tag names whose body is lexed verbatim, as a lookup map for isset().
     *
     * @return array<string, true>
     */
    public function rawBodyTags(): array
    {
        return $this->rawBodyTags ??= array_fill_keys(
            array_keys(array_filter(
                $this->tags,
                fn (string $tag): bool => is_subclass_of($tag, TagBlock::class) && $tag::hasRawBody()
            )),
            true
        );
    }

    /**
     * @return class-string|null
     */
    public function get(string $name): ?string
    {
        return $this->tags[$name] ?? null;
    }

    /**
     * @return array<string, class-string<Tag>>
     */
    public function all(): array
    {
        return $this->tags;
    }
}
