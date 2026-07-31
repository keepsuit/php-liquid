<?php

use Keepsuit\Liquid\Compiler\Cache\FilesystemCompiledTemplateCache;
use Keepsuit\Liquid\Compiler\CompiledTemplate;
use Keepsuit\Liquid\Compiler\CompiledTemplateInterface;
use Keepsuit\Liquid\EnvironmentFactory;
use Keepsuit\Liquid\Template;

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

        /** @var Template $compiled */
        $compiled = require $path;

        expect($compiled)->toBeInstanceOf(Template::class);
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

    try {
        file_put_contents($directory.'/corrupt.php', '<?php return ; not valid');
        file_put_contents($directory.'/wrong.php', '<?php return new stdClass;');

        expect($cache->get('corrupt'))->toBeNull();
        expect($cache->get('wrong'))->toBeNull();

        $source = "<?php\nreturn new \\Keepsuit\\Liquid\\Compiler\\CompiledTemplate(\n    new \\Keepsuit\\Liquid\\Nodes\\Document(new \\Keepsuit\\Liquid\\Nodes\\BodyNode),\n);\n";
        $cache->set('valid', $source);

        expect($cache->get('valid'))->toBeInstanceOf(CompiledTemplateInterface::class);
        expect(glob($directory.'/.valid.php.tmp-*'))->toBe([]);
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

test('compiled value decoding rejects malformed payloads and incomplete classes', function () {
    expect(fn () => CompiledTemplate::decodeValue('not-valid-base64!'))
        ->toThrow(RuntimeException::class);
    expect(fn () => CompiledTemplate::decodeValue(base64_encode('not serialized')))
        ->toThrow(RuntimeException::class);
    expect(fn () => CompiledTemplate::decodeValue(base64_encode('O:12:"MissingClass":0:{}')))
        ->toThrow(RuntimeException::class);

    expect(CompiledTemplate::decodeValue(base64_encode('b:0;')))->toBeFalse();
});
