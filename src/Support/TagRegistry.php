<?php

namespace Keepsuit\Liquid\Support;

use Keepsuit\Liquid\Tag;

class TagRegistry
{
    /**
     * @var array<string, class-string<Tag>>
     */
    protected array $tags = [];

    /**
     * @param  class-string<Tag>  $tag
     * @return $this
     */
    public function register(string $tag): static
    {
        $this->tags[$tag::tagName()] = $tag;

        return $this;
    }

    public function delete(string $name): static
    {
        unset($this->tags[$name]);

        return $this;
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
