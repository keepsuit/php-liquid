<?php

use Keepsuit\Liquid\Compiler\CompiledTemplate;
use Keepsuit\Liquid\Compiler\CompilerContext;
use Keepsuit\Liquid\EnvironmentFactory;
use Keepsuit\Liquid\Nodes\Node;
use Keepsuit\Liquid\Render\RenderContext;

class CompilerSafetyResourceParent extends Node
{
    public function __construct(private mixed $state) {}

    public function render(RenderContext $context): string
    {
        return 'resource node';
    }
}

class CompilerSafetyResourceChild extends CompilerSafetyResourceParent
{
    private string $state = 'child state';
}

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

/** @return array{exitCode:int,output:string} */
function compilerArtifactSafetySubprocess(string $source): array
{
    $source = 'require '.var_export(dirname(__DIR__, 2).'/vendor/autoload.php', true).';'.$source;
    exec(escapeshellarg(PHP_BINARY).' -d max_execution_time=10 -r '.escapeshellarg($source).' 2>&1', $output, $exitCode);

    return ['exitCode' => $exitCode, 'output' => implode("\n", $output)];
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

test('compiler object validation safely traverses recursive array references', function () {
    $cycle = [];
    $cycle['self'] = &$cycle;
    $object = (object) ['data' => $cycle];
    $object->self = $object;

    $result = compilerArtifactSafetySubprocess(<<<'PHP'
        $cycle = [];
        $cycle['self'] = &$cycle;
        $object = (object) ['data' => $cycle];
        $object->self = $object;
        $source = (new \Keepsuit\Liquid\Compiler\CompilerContext)->writeValue($object);
        $decoded = eval('return '.$source.';');
        if ($decoded->self !== $decoded) {
            throw new RuntimeException('Object cycle was not preserved.');
        }
        echo base64_encode(serialize($decoded));
        PHP);

    expect($result['exitCode'])->toBe(0);
    expect($result['output'])->toBe(base64_encode(serialize($object)));
});

test('compiler object validation checks resources after a recursive array edge', function () {
    $result = compilerArtifactSafetySubprocess(<<<'PHP'
        $resource = fopen('php://memory', 'r');
        $cycle = [];
        $cycle['self'] = &$cycle;
        $cycle['resource'] = $resource;
        $object = (object) ['data' => $cycle];
        try {
            (new \Keepsuit\Liquid\Compiler\CompilerContext)->writeValue($object);
            echo 'not rejected';
        } catch (RuntimeException $exception) {
            echo $exception->getMessage();
        } finally {
            fclose($resource);
        }
        PHP);

    expect($result['exitCode'])->toBe(0);
    expect($result['output'])->toBe('Unable to safely encode a compiler value containing a resource.');
});

test('inline compiler arrays reject cycles and retain acyclic shared references', function (string $method) {
    $result = compilerArtifactSafetySubprocess('$method = '.var_export($method, true).';'.<<<'PHP'
        $cycle = [];
        $cycle['self'] = &$cycle;
        $context = new \Keepsuit\Liquid\Compiler\CompilerContext;
        try {
            $context->$method($cycle);
            echo 'not rejected';
        } catch (RuntimeException $exception) {
            echo $exception->getMessage();
        }
        $shared = ['value'];
        $array = [&$shared, &$shared];
        if (eval('return '.$context->$method($array).';') !== [['value'], ['value']]) {
            throw new RuntimeException('Shared acyclic array did not round trip.');
        }
        PHP);

    expect($result['exitCode'])->toBe(0);
    expect($result['output'])->toBe('Unable to safely encode a recursive compiler array.');
})->with(['writeValue', 'writeCachedValue']);

test('inherited private resources cannot replace a published compiled artifact', function () {
    $directory = compilerArtifactSafetyDirectory();
    $path = compilerArtifactSafetyPath($directory);
    $environment = EnvironmentFactory::new()->build();
    $resource = fopen('php://memory', 'r');

    try {
        $environment->compile($environment->parseString('safe artifact'), $path);
        $original = file_get_contents($path);
        $unsafe = $environment->parseString('');
        $unsafe->root->body->pushChild(new CompilerSafetyResourceChild($resource));

        expect(fn () => $environment->compile($unsafe, $path))
            ->toThrow(RuntimeException::class, 'Unable to safely reconstruct fallback node');
        expect(file_get_contents($path))->toBe($original);
        expect(glob($directory.'/.compiled.php.tmp-*'))->toBe([]);
        expect((require $path)->render($environment->newRenderContext()))->toBe('safe artifact');
    } finally {
        fclose($resource);
        removeCompilerArtifactSafetyDirectory($directory);
    }
});

test('compiled artifacts use permissions derived from the umask', function (int $mask) {
    $directory = compilerArtifactSafetyDirectory();
    $path = compilerArtifactSafetyPath($directory);
    $environment = EnvironmentFactory::new()->build();
    $template = $environment->parseString('permissions');
    $previousMask = umask($mask);

    try {
        $environment->compile($template, $path);
        clearstatcache(true, $path);

        expect(fileperms($path) & 0777)->toBe(0666 & ~$mask);
    } finally {
        umask($previousMask);
        removeCompilerArtifactSafetyDirectory($directory);
    }
})->with([0022, 0002]);

test('recompiling an artifact at the same path updates its output and opcache timestamp', function () {
    $directory = compilerArtifactSafetyDirectory();
    $path = compilerArtifactSafetyPath($directory);
    $environment = EnvironmentFactory::new()->build();
    $requestTime = $_SERVER['REQUEST_TIME'];
    $_SERVER['REQUEST_TIME'] = time();

    try {
        $environment->compile($environment->parseString('first'), $path);
        $first = require $path;
        expect($first->render($environment->newRenderContext()))->toBe('first');

        $environment->compile($environment->parseString('second'), $path);
        clearstatcache(true, $path);
        $second = require $path;

        expect(filemtime($path))->toBe($_SERVER['REQUEST_TIME'] - 5);
        expect($second->render($environment->newRenderContext()))->toBe('second');
    } finally {
        $_SERVER['REQUEST_TIME'] = $requestTime;
        removeCompilerArtifactSafetyDirectory($directory);
    }
});

test('compiling an artifact without a request time backdates it from the current time', function () {
    $directory = compilerArtifactSafetyDirectory();
    $path = compilerArtifactSafetyPath($directory);
    $environment = EnvironmentFactory::new()->build();
    $requestTime = $_SERVER['REQUEST_TIME'];
    unset($_SERVER['REQUEST_TIME']);

    try {
        $before = time();
        $environment->compile($environment->parseString('ok'), $path);
        clearstatcache(true, $path);

        expect(filemtime($path))->toBeGreaterThanOrEqual($before - 5)->toBeLessThanOrEqual(time() - 5);
    } finally {
        $_SERVER['REQUEST_TIME'] = $requestTime;
        removeCompilerArtifactSafetyDirectory($directory);
    }
});
