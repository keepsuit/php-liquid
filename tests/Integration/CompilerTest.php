<?php

use Keepsuit\Liquid\EnvironmentFactory;
use Keepsuit\Liquid\Template;

function temporaryCompiledTemplatePath(): string
{
    $path = tempnam(sys_get_temp_dir(), 'liquid-compiled-');

    if ($path === false) {
        throw new RuntimeException('Unable to create a temporary compiled template path.');
    }

    unlink($path);

    return $path.'.php';
}

test('environment compiles a template to a requireable artifact', function () {
    $environment = EnvironmentFactory::new()->build();
    $template = $environment->parseString('Hello {{ name }}');
    $compiledPath = temporaryCompiledTemplatePath();

    try {
        $environment->compile($template, $compiledPath);

        expect($compiledPath)->toBeFile();

        /** @var Template $compiled */
        $compiled = require $compiledPath;

        expect($compiled)->toBeInstanceOf(Template::class);
        expect($compiled->render($environment->newRenderContext(data: ['name' => 'World'])))
            ->toBe('Hello World');
    } finally {
        @unlink($compiledPath);
    }
});

test('compilation does not change interpreted template rendering', function () {
    $environment = EnvironmentFactory::new()->build();
    $template = $environment->parseString('Hello {{ name }}');
    $compiledPath = temporaryCompiledTemplatePath();

    try {
        $environment->compile($template, $compiledPath);

        expect($template->render($environment->newRenderContext(data: ['name' => 'World'])))
            ->toBe('Hello World');
    } finally {
        @unlink($compiledPath);
    }
});
