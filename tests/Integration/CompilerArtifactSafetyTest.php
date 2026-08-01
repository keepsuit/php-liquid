<?php

use Keepsuit\Liquid\Compiler\Cache\FilesystemCompiledTemplateCache;
use Keepsuit\Liquid\Compiler\CompiledTemplateInterface;
use Keepsuit\Liquid\Compiler\Compiler;
use Keepsuit\Liquid\Compiler\CompilerContext;
use Keepsuit\Liquid\EnvironmentFactory;
use Keepsuit\Liquid\ParsedTemplate;

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

        /** @var CompiledTemplateInterface $compiled */
        $compiled = require $path;

        expect($compiled)->toBeInstanceOf(CompiledTemplateInterface::class);
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

test('filesystem compiler cache publishes atomically and fails closed on invalid artifacts', function () {
    $directory = compilerArtifactSafetyDirectory();
    $cache = new FilesystemCompiledTemplateCache($directory);
    $environment = EnvironmentFactory::new()->build();

    try {
        file_put_contents($directory.'/corrupt.php', '<?php return ; not valid');
        file_put_contents($directory.'/wrong.php', '<?php return new stdClass;');

        expect($cache->get('corrupt'))->toBeNull();
        expect($cache->get('wrong'))->toBeNull();

        $template = $environment->parseString('valid artifact');
        assert($template instanceof ParsedTemplate);
        $cache->set('valid', (new Compiler)->compile($template));

        expect($cache->get('valid'))->toBeInstanceOf(CompiledTemplateInterface::class);
        expect(glob($directory.'/.valid.php.tmp-*'))->toBe([]);

        expect(fn () => $cache->set('valid', '<?php return null;'))
            ->toThrow(RuntimeException::class);
        expect($cache->get('valid'))->toBeInstanceOf(CompiledTemplateInterface::class);
    } finally {
        removeCompilerArtifactSafetyDirectory($directory);
    }
});

test('filesystem compiler cache leaves its target untouched when publication fails', function () {
    $directory = compilerArtifactSafetyDirectory();
    $cache = new FilesystemCompiledTemplateCache($directory);
    mkdir($directory.'/blocked.php');

    try {
        expect(fn () => $cache->set('blocked', '<?php return null;'))
            ->toThrow(RuntimeException::class);
        expect($directory.'/blocked.php')->toBeDirectory();
        expect(glob($directory.'/.blocked.php.tmp-*'))->toBe([]);
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
