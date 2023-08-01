<?php

namespace Keepsuit\Liquid;

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
     * @return array<string, class-string<Tag>>
     */
    public function all(): array
    {
        return $this->tags;
    }
}
