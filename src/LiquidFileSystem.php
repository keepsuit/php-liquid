<?php

namespace Keepsuit\Liquid;

interface LiquidFileSystem
{
    public function readTemplateFile(string $templatePath): string;
}
