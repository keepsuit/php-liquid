<?php

namespace Keepsuit\Liquid\Performance;

use Keepsuit\Liquid\Environment;
use Keepsuit\Liquid\Performance\Shopify\Database;
use Keepsuit\Liquid\Support\Arr;

class ThemeRunner
{
    /**
     * @var array<ThemeTestTemplate>
     */
    protected array $tests;

    /**
     * @var array<CompiledThemeTestTemplate>
     */
    protected array $compiledTemplates;

    public function __construct(
        protected Environment $environment
    ) {
        $files = glob(__DIR__.'/tests/**/*.liquid');

        if ($files === false) {
            throw new \RuntimeException('Could not find any tests');
        }

        $this->tests = Arr::compact(Arr::map($files, function (string $path) {
            if (basename($path) === 'theme.liquid') {
                return null;
            }

            $themePath = dirname($path).'/theme.liquid';

            return new ThemeTestTemplate(
                environment: $this->environment,
                templateName: $path,
                liquid: file_get_contents($path) ?: '',
                layoutLiquid: file_exists($themePath) ? (file_get_contents($themePath) ?: '') : null,
            );
        }));

        $this->compileAllTests();
    }

    public function compile(): void
    {
        foreach ($this->tests as $test) {
            $this->environment->parseString($test->liquid);
            if ($test->layoutLiquid !== null) {
                $this->environment->parseString($test->layoutLiquid);
            }
        }
    }

    public function render(): void
    {
        $database = [...Database::tables()];

        foreach ($this->compiledTemplates as $compiled) {
            $compiled->render($database);
        }
    }

    public function stream(): void
    {
        $database = [...Database::tables()];

        foreach ($this->compiledTemplates as $compiled) {
            $compiled->stream($database);
        }
    }

    public function run(): void
    {
        $database = [...Database::tables()];

        foreach ($this->tests as $test) {
            $compiled = $test->compile();
            $compiled->render($database);
        }
    }

    public function runStreaming(): void
    {
        $database = [...Database::tables()];

        foreach ($this->tests as $test) {
            $compiled = $test->compile();
            $compiled->stream($database);
        }
    }

    protected function compileAllTests(): void
    {
        $this->compiledTemplates = Arr::map($this->tests, fn (ThemeTestTemplate $template) => $template->compile());
    }
}
