<?php

namespace Keepsuit\Liquid\Tests\Stubs;

use Keepsuit\Liquid\Contracts\LiquidFileSystem;

class ProfilingFileSystem implements LiquidFileSystem
{
    public function readTemplateFile(string $templateName): string
    {
        return sprintf("Rendering template {%% assign template_name = '%s'%%}\n{{ template_name }}", $templateName);
    }
}
