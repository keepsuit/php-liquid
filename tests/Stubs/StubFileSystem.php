<?php

namespace Keepsuit\Liquid\Tests\Stubs;

use Keepsuit\Liquid\Contracts\LiquidFileSystem;

class StubFileSystem implements LiquidFileSystem
{
    public int $fileReadCount = 0;

    public function __construct(
        protected array $partials = [],
    ) {}

    public function readTemplateFile(string $templateName): string
    {
        $this->fileReadCount += 1;

        return $this->partials[$templateName] ?? throw new \RuntimeException('Template not found');
    }
}
