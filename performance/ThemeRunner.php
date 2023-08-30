<?php

namespace Keepsuit\Liquid\Performance;

use Keepsuit\Liquid\Performance\Shopify\Database;
use Keepsuit\Liquid\Support\Arr;
use Keepsuit\Liquid\Template;

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

    public function __construct()
    {
        $files = glob(__DIR__.'/tests/**/*.liquid');

        $this->tests = Arr::compact(Arr::map($files, function (string $path) {
            if (basename($path) === 'theme.liquid') {
                return null;
            }

            $themePath = dirname($path).'/theme.liquid';

            return new ThemeTestTemplate(
                templateName: $path,
                liquid: file_get_contents($path),
                layoutLiquid: file_exists($themePath) ? file_get_contents($themePath) : null,
            );
        }));

        $this->compileAllTests();
    }

    public function compile(): void
    {
        foreach ($this->tests as $test) {
            Template::parse($test->liquid);
            if ($test->layoutLiquid !== null) {
                Template::parse($test->layoutLiquid);
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

    public function run(): void
    {
        foreach ($this->tests as $test) {
            $compiled = $test->compile();
            $compiled->render();
        }
    }

    protected function compileAllTests(): void
    {
        $this->compiledTemplates = Arr::map($this->tests, fn (ThemeTestTemplate $template) => $template->compile());
    }
}
