<?php

namespace Keepsuit\Liquid\Performance\Support\Drops;

use Keepsuit\Liquid\Drop;

final class ArticleDrop extends Drop
{
    /**
     * @param  list<string>  $tags
     */
    public function __construct(
        public readonly string $handle,
        public readonly string $title,
        public readonly string $excerpt,
        public readonly string $author,
        public readonly string $publishedAt,
        public readonly int $readingMinutes,
        public readonly array $tags,
        public readonly ImageDrop $image,
    ) {}

    public function url(): string
    {
        return '/blogs/journal/'.$this->handle;
    }
}
