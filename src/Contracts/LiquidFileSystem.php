<?php

namespace Keepsuit\Liquid\Contracts;

interface LiquidFileSystem
{
    public function readTemplateFile(string $templateName): string;
}
