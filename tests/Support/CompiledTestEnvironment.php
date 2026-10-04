<?php

namespace Keepsuit\Liquid\Tests\Support;

use Keepsuit\Liquid\Compiler\CompiledTemplate;
use Keepsuit\Liquid\Environment;
use Keepsuit\Liquid\Parse\ParseContext;
use Keepsuit\Liquid\ParsedTemplate;
use Keepsuit\Liquid\Template;
use ReflectionClass;

/** Run the existing rendering tests with compiled roots and partials. */
class CompiledTestEnvironment extends Environment
{
    public static function wrap(Environment $environment): self
    {
        if ($environment instanceof self) {
            return $environment;
        }

        $reflection = new ReflectionClass(self::class);
        $compiledEnvironment = $reflection->newInstanceWithoutConstructor();

        // Preserve the exact registries, extensions, caches and options built by
        // each test, including custom filter instances and post-build changes.
        foreach ((new ReflectionClass(Environment::class))->getProperties() as $property) {
            if ($property->isStatic()) {
                continue;
            }

            $value = $property->getValue($environment);
            if ($property->getName() === 'templatesCache') {
                $value = new CompiledTestTemplatesCache($environment->templatesCache, $environment);
            }

            $property->setValue($compiledEnvironment, $value);
        }

        return $compiledEnvironment;
    }

    public function newParseContext(): ParseContext
    {
        return new CompiledTestParseContext(environment: $this);
    }

    public static function compileTemplate(Environment $environment, Template $template): CompiledTemplate
    {
        if ($template instanceof CompiledTemplate) {
            return $template;
        }

        $path = tempnam(sys_get_temp_dir(), 'liquid-test-compiled-');
        if ($path === false) {
            throw new \RuntimeException('Unable to create a compiled test template.');
        }

        try {
            assert($template instanceof ParsedTemplate);
            $environment->compile($template, $path);
            $compiled = require $path;
            assert($compiled instanceof CompiledTemplate);

            // Artifacts start with an empty state; retain parsing metadata used
            // by the same assertions in the interpreted suite.
            return new ($compiled::class)(clone $template->getState());
        } finally {
            unlink($path);
        }
    }
}
