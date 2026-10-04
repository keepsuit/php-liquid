<?php

use Keepsuit\Liquid\Compiler\CompiledTemplate;
use Keepsuit\Liquid\EnvironmentFactory;
use Keepsuit\Liquid\ParsedTemplate;
use Keepsuit\Liquid\TemplatesCache\CompiledTemplatesCache;
use Keepsuit\Liquid\Tests\Stubs\StubFileSystem;

test('compiled cache compiles misses and loads roots and partials from disk', function (bool $lazyParsing) {
    $path = __DIR__.'/../cache/compiled-miss';
    $cache = new CompiledTemplatesCache($path);
    $cache->clear();
    $fileSystem = new StubFileSystem([
        'hello' => "Hello {% render 'name', name: name %}!",
        'name' => '{{ name }}',
    ]);
    $environment = EnvironmentFactory::new()
        ->setFilesystem($fileSystem)
        ->setTemplatesCache($cache)
        ->setLazyParsing($lazyParsing)
        ->build();

    $template = $environment->parseTemplate('hello');
    expect($template)->toBeInstanceOf(ParsedTemplate::class)
        ->and($cache->has('hello'))->toBeTrue()
        ->and($template->render($environment->newRenderContext(data: ['name' => 'John'])))->toBe('Hello John!')
        ->and($fileSystem->fileReadCount)->toBe(2);

    $reader = new CompiledTemplatesCache($path);
    $environment = EnvironmentFactory::new()
        ->setFilesystem($fileSystem)
        ->setTemplatesCache($reader)
        ->setLazyParsing($lazyParsing)
        ->build();
    $compiled = $environment->parseTemplate('hello');

    expect($compiled)->toBeInstanceOf(CompiledTemplate::class)
        ->and($reader->get('name'))->toBeInstanceOf(CompiledTemplate::class)
        ->and($compiled->render($environment->newRenderContext(data: ['name' => 'Jane'])))->toBe('Hello Jane!')
        ->and(implode('', iterator_to_array($compiled->stream($environment->newRenderContext(data: ['name' => 'Jane'])))))->toBe('Hello Jane!')
        ->and($fileSystem->fileReadCount)->toBe(2);

    $reader->remove('hello');
    expect($reader->get('hello'))->toBeNull();
    $environment->parseTemplate('hello');
    expect($fileSystem->fileReadCount)->toBe(3);

    $reader->clear();
    expect($reader->get('hello'))->toBeNull()
        ->and($reader->get('name'))->toBeNull();
})->with([false, true]);

test('compiled cache follows the filesystem memory retention option', function (bool $keepInMemory) {
    $path = __DIR__.'/../cache/compiled-memory';
    $writer = new CompiledTemplatesCache($path);
    $writer->clear();
    $parsed = parseSource('Hello {{ name }}');
    $writer->set('test', $parsed);
    expect($writer->get('test'))->toBe($parsed);

    $reader = new CompiledTemplatesCache($path, keepInMemory: $keepInMemory);
    $first = $reader->get('test');
    $second = $reader->get('test');

    expect($first)->toBeInstanceOf(CompiledTemplate::class);
    if ($keepInMemory) {
        expect($second)->toBe($first);
    } else {
        expect($second)->not->toBe($first);
    }

    $reader->clear();
    expect($reader->get('test'))->toBeNull();
})->with([false, true]);

test('corrupted compiled artifacts are cache misses and can be rebuilt', function (string $source) {
    $path = __DIR__.'/../cache/compiled-corrupted';
    $cache = new CompiledTemplatesCache($path, keepInMemory: false);
    $cache->clear();
    $artifactPath = $path.'/'.hash('sha256', 'hello').'.php';
    file_put_contents($artifactPath, $source);

    expect($cache->has('hello'))->toBeTrue()
        ->and($cache->get('hello'))->toBeNull();

    $fileSystem = new StubFileSystem(['hello' => 'Hello {{ name }}']);
    $environment = EnvironmentFactory::new()
        ->setFilesystem($fileSystem)
        ->setTemplatesCache($cache)
        ->build();
    $environment->parseTemplate('hello');
    $compiled = $environment->parseTemplate('hello');

    expect($compiled)->toBeInstanceOf(CompiledTemplate::class)
        ->and($compiled->render($environment->newRenderContext(data: ['name' => 'John'])))->toBe('Hello John')
        ->and($fileSystem->fileReadCount)->toBe(1);

    $cache->clear();
})->with([
    'invalid return value' => '<?php return false;',
    'invalid PHP' => '<?php return (',
    'load exception' => '<?php throw new RuntimeException("Broken artifact");',
]);
