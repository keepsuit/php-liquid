<?php

namespace Keepsuit\Liquid\Support;

use Keepsuit\Liquid\Tag;
use Keepsuit\Liquid\Tags;

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

    /**
     * Returns TagRegistry instance with standard tags registered.
     */
    public static function default(): TagRegistry
    {
        return (new TagRegistry)
            ->register(Tags\AssignTag::class)
            ->register(Tags\BreakTag::class)
            ->register(Tags\CaptureTag::class)
            ->register(Tags\CaseTag::class)
            ->register(Tags\ContinueTag::class)
            ->register(Tags\CycleTag::class)
            ->register(Tags\DecrementTag::class)
            ->register(Tags\EchoTag::class)
            ->register(Tags\ForTag::class)
            ->register(Tags\IfChanged::class)
            ->register(Tags\IfTag::class)
            ->register(Tags\IncrementTag::class)
            ->register(Tags\LiquidTag::class)
            ->register(Tags\RenderTag::class)
            ->register(Tags\TableRowTag::class)
            ->register(Tags\UnlessTag::class);
    }
}
