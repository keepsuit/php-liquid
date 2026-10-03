<?php

namespace Keepsuit\Liquid\Tests\Support;

use Keepsuit\Liquid\Parse\ParseContext;
use Keepsuit\Liquid\Parse\TokenStream;
use Keepsuit\Liquid\Template;

class CompiledTestParseContext extends ParseContext
{
    public function parse(TokenStream|string $source, ?string $name = null): Template
    {
        return CompiledTestEnvironment::compileTemplate($this->environment, parent::parse($source, $name));
    }

    public function loadPartial(string $templateName): Template
    {
        // ParseContext creates a base ParseContext for an uncached partial.
        // Convert that first returned instance as well as the cached instance.
        return CompiledTestEnvironment::compileTemplate($this->environment, parent::loadPartial($templateName));
    }
}
