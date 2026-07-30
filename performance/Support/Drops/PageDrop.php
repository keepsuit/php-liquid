<?php

namespace Keepsuit\Liquid\Performance\Support\Drops;

use Keepsuit\Liquid\Drop;

final class PageDrop extends Drop
{
    /**
     * @param  list<array{title: string, body: string}>  $sections
     */
    public function __construct(
        public readonly string $handle,
        public readonly string $title,
        public readonly string $content,
        public readonly array $sections,
    ) {}

    public function url(): string
    {
        return '/pages/'.$this->handle;
    }
}
