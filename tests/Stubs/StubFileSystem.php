<?php

namespace Keepsuit\Liquid\Tests\Stubs;

use Keepsuit\Liquid\LiquidFileSystem;

class StubFileSystem implements LiquidFileSystem
{
    public int $fileReadCount = 0;

    public function __construct(
        protected array $partials = [],
    ) {
    }

    public function readTemplateFile(string $templatePath): string
    {
        $this->fileReadCount += 1;

        return $this->partials[$templatePath] ?? '';
    }
}
