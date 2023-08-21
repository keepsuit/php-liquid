<?php

namespace Keepsuit\Liquid\FileSystems;

use Keepsuit\Liquid\Exceptions\FileSystemException;
use Keepsuit\Liquid\LiquidFileSystem;

class BlankFileSystem implements LiquidFileSystem
{
    public function readTemplateFile(string $templatePath): string
    {
        throw new FileSystemException('This liquid context does not allow includes.');
    }
}
