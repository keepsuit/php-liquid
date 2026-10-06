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
    expect($template)->toBeInstanceOf(CompiledTemplate::class)
        ->and($cache->has('hello'))->toBeTrue()
        ->and($cache->get('hello'))->toBeInstanceOf(CompiledTemplate::class)
        ->and($environment->parseTemplate('hello'))->toBeInstanceOf(CompiledTemplate::class)
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
    $writer = new class($path, keepInMemory: $keepInMemory) extends CompiledTemplatesCache
    {
        public int $loads = 0;

        protected function loadCompiledTemplate(string $compiledPath): ?\Keepsuit\Liquid\Template
        {
            $this->loads++;

            return parent::loadCompiledTemplate($compiledPath);
        }
    };
    $writer->clear();
    $writer->set('test', parseSource('Hello {{ name }}'));
    $written = $writer->get('test');
    expect($written)->toBeInstanceOf(CompiledTemplate::class)
        ->and($written?->render(buildRenderContext(data: ['name' => 'John'])))->toBe('Hello John')
        ->and($writer->loads)->toBe(1);

    $writer->set('test', parseSource('Welcome {{ name }}'));
    $replacement = $writer->get('test');
    expect($replacement)->toBeInstanceOf(CompiledTemplate::class)
        ->not->toBe($written)
        ->and($replacement?->render(buildRenderContext(data: ['name' => 'Jane'])))->toBe('Welcome Jane')
        ->and($writer->loads)->toBe(2);

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

test('artifacts that do not return a compiled template are cache misses and can be rebuilt', function () {
    $path = __DIR__.'/../cache/compiled-corrupted';
    $cache = new CompiledTemplatesCache($path, keepInMemory: false);
    $cache->clear();
    $artifactPath = $path.'/'.hash('sha256', 'hello').'.php';
    file_put_contents($artifactPath, '<?php return false;');

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
});

test('an artifact removed between existence check and load is a cache miss', function () {
    $cache = new class(__DIR__.'/../cache/compiled-missing') extends CompiledTemplatesCache
    {
        public function load(string $compiledPath): ?\Keepsuit\Liquid\Template
        {
            return $this->loadCompiledTemplate($compiledPath);
        }
    };

    expect($cache->load(__DIR__.'/../cache/compiled-missing/absent.php'))->toBeNull();
    $cache->clear();
});

test('artifacts that fail to load surface the error', function (string $source, string $exception) {
    $path = __DIR__.'/../cache/compiled-broken';
    $cache = new CompiledTemplatesCache($path, keepInMemory: false);
    $cache->clear();
    file_put_contents($path.'/'.hash('sha256', 'hello').'.php', $source);

    try {
        expect(fn () => $cache->get('hello'))->toThrow($exception);
    } finally {
        $cache->clear();
    }
})->with([
    'invalid PHP' => ['<?php return (', ParseError::class],
    'load exception' => ['<?php throw new RuntimeException("Broken artifact");', RuntimeException::class],
]);

test('compiled cache preserves partials and outputs collected while parsing', function () {
    $path = __DIR__.'/../cache/compiled-state';
    $cache = new CompiledTemplatesCache($path);
    $cache->clear();
    $environment = EnvironmentFactory::new()
        ->setFilesystem(new StubFileSystem(['snippet' => 'hi']))
        ->setTemplatesCache($cache)
        ->build();

    $state = $environment->parseString('{% render "snippet" %}')->getState();
    $state->outputs->set('scalar', 'value');
    $state->outputs->set('object', new ArrayObject(['a' => 1]));
    $template = new ParsedTemplate(root: $environment->parseString('{% render "snippet" %}')->root, state: $state);

    $cache->set('main', $template);
    $compiledState = (new CompiledTemplatesCache($path))->get('main')->getState();

    expect($compiledState->partials)->toBe(['snippet'])
        ->and($compiledState->outputs->get('scalar'))->toBe('value')
        ->and($compiledState->outputs->get('object'))->toEqual(new ArrayObject(['a' => 1]))
        ->and($compiledState->errors)->toBe([]);

    $cache->clear();
});

test('compiled cache rejects outputs that cannot be serialized', function (Closure $value) {
    $path = __DIR__.'/../cache/compiled-outputs';
    $cache = new CompiledTemplatesCache($path);
    $cache->clear();
    $environment = EnvironmentFactory::new()->setTemplatesCache($cache)->build();

    $state = $environment->parseString('hello', name: 'main')->getState();
    $state->outputs->set('key', ['nested' => $value()]);
    $template = new ParsedTemplate(root: $environment->parseString('hello', name: 'main')->root, state: $state);

    expect(fn () => $cache->set('main', $template))
        ->toThrow(RuntimeException::class, 'Unable to serialize the outputs of template main.');

    expect($cache->has('main'))->toBeFalse();
})->with([
    'closure' => [fn () => fn () => fn () => 1],
    'resource' => [fn () => fn () => fopen('php://memory', 'r')],
]);
