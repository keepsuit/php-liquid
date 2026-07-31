<?php

namespace Keepsuit\Liquid\TemplatesCache;

use Keepsuit\Liquid\TemplateInterface;

class SerializeTemplatesCache extends FilesystemTemplatesCache
{
    protected function saveCompiledTemplate(string $compiledPath, TemplateInterface $template): void
    {
        file_put_contents($compiledPath, serialize($template));
    }

    protected function loadCompiledTemplate(string $compiledPath): ?TemplateInterface
    {
        try {
            $content = file_get_contents($compiledPath);

            if ($content === false) {
                return null;
            }

            $template = unserialize($content);

            if (! $template instanceof TemplateInterface) {
                return null;
            }

            return $template;
        } catch (\Throwable) {
            return null;
        }
    }
}
