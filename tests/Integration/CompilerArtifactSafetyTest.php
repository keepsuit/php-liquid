<?php

use Keepsuit\Liquid\Compiler\CompiledTemplate;
use Keepsuit\Liquid\Compiler\CompilerContext;
use Keepsuit\Liquid\EnvironmentFactory;

function compilerArtifactSafetyDirectory(): string
{
    $directory = sys_get_temp_dir().'/liquid-compiler-safety-'.bin2hex(random_bytes(8));

    if (! mkdir($directory, 0755, true) && ! is_dir($directory)) {
        throw new RuntimeException('Unable to create compiler safety test directory.');
    }

    return $directory;
}

function compilerArtifactSafetyPath(string $directory, string $name = 'compiled.php'): string
{
    return $directory.'/'.$name;
}

function removeCompilerArtifactSafetyDirectory(string $directory): void
{
    foreach (glob($directory.'/*') ?: [] as $path) {
        if (is_dir($path)) {
            rmdir($path);
        } else {
            unlink($path);
        }
    }

    foreach (glob($directory.'/.*') ?: [] as $path) {
        if (basename($path) === '.' || basename($path) === '..') {
            continue;
        }

        if (is_dir($path)) {
            rmdir($path);
        } else {
            unlink($path);
        }
    }

    rmdir($directory);
}

test('environment publishes compiled artifacts atomically', function () {
    $directory = compilerArtifactSafetyDirectory();
    $path = compilerArtifactSafetyPath($directory);
    $environment = EnvironmentFactory::new()->build();
    $template = $environment->parseString('safe artifact');

    try {
        $environment->compile($template, $path);

        expect($path)->toBeFile();
        expect(glob($directory.'/.compiled.php.tmp-*'))->toBe([]);

        /** @var CompiledTemplate $compiled */
        $compiled = require $path;

        expect($compiled)->toBeInstanceOf(CompiledTemplate::class);
    } finally {
        removeCompilerArtifactSafetyDirectory($directory);
    }
});

test('environment removes staged artifacts when publication fails', function () {
    $directory = compilerArtifactSafetyDirectory();
    $path = compilerArtifactSafetyPath($directory);
    mkdir($path);
    $environment = EnvironmentFactory::new()->build();
    $template = $environment->parseString('safe artifact');

    try {
        expect(fn () => $environment->compile($template, $path))
            ->toThrow(RuntimeException::class);
        expect($path)->toBeDirectory();
        expect(glob($directory.'/.compiled.php.tmp-*'))->toBe([]);
    } finally {
        removeCompilerArtifactSafetyDirectory($directory);
    }
});

test('compiler value export rejects resources', function () {
    $resource = fopen('php://memory', 'r');

    if ($resource === false) {
        throw new RuntimeException('Unable to open resource for compiler safety test.');
    }

    try {
        expect(fn () => (new CompilerContext)->writeValue($resource))
            ->toThrow(RuntimeException::class);
    } finally {
        fclose($resource);
    }
});
