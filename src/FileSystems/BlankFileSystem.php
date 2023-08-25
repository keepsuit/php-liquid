<?php

namespace Keepsuit\Liquid\FileSystems;

use Keepsuit\Liquid\Contracts\LiquidFileSystem;
use Keepsuit\Liquid\Exceptions\FileSystemException;

class BlankFileSystem implements LiquidFileSystem
{
    public function readTemplateFile(string $templatePath): string
    {
        throw new FileSystemException('This liquid context does not allow includes.');
    }
}
