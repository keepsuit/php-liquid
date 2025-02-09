<?php

namespace Keepsuit\Liquid\TemplatesCache;

use Keepsuit\Liquid\Template;

class SerializeTemplatesCache extends FilesystemTemplatesCache
{
    protected function saveCompiledTemplate(string $compiledPath, Template $template): void
    {
        file_put_contents($compiledPath, serialize($template));
    }

    protected function loadCompiledTemplate(string $compiledPath): ?Template
    {
        try {
            $content = file_get_contents($compiledPath);

            if ($content === false) {
                return null;
            }

            $template = unserialize($content);

            if (! $template instanceof Template) {
                return null;
            }

            return $template;
        } catch (\Throwable) {
            return null;
        }
    }
}
