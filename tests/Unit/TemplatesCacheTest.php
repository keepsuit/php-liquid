<?php

use Keepsuit\Liquid\Contracts\LiquidTemplatesCache;
use Spatie\TemporaryDirectory\TemporaryDirectory;

beforeEach(function () {
    $this->tempDir = TemporaryDirectory::make()->deleteWhenDestroyed()->path();
});

test('templates cache', function (LiquidTemplatesCache $cache) {
    $cache->clear();

    expect($cache)
        ->has('test')->toBe(false)
        ->get('test')->toBeNull();

    $template = parseSource('Hello {{ name }}');

    $cache->set('test', $template);
    expect($cache)
        ->has('test')->toBe(true)
        ->get('test')->toBeInstanceOf(\Keepsuit\Liquid\Template::class);

    $renderContext = new \Keepsuit\Liquid\Render\RenderContext(['name' => 'John']);
    $cachedTemplate = $cache->get('test');
    expect($cachedTemplate->render($renderContext))->toBe('Hello John');

    $cache->remove('test');
    expect($cache)
        ->has('test')->toBe(false)
        ->get('test')->toBeNull();

    $cache->set('test', $template);
    expect($cache)->has('test')->toBe(true);
    $cache->clear();
    expect($cache)->has('test')->toBe(false);
})->with([
    'memory' => fn () => new \Keepsuit\Liquid\TemplatesCache\MemoryTemplatesCache,
    'serialize' => fn () => new \Keepsuit\Liquid\TemplatesCache\SerializeTemplatesCache($this->tempDir, keepInMemory: false),
    'serialize & memory' => fn () => new \Keepsuit\Liquid\TemplatesCache\SerializeTemplatesCache($this->tempDir, keepInMemory: true),
    'var export' => fn () => new \Keepsuit\Liquid\TemplatesCache\VarExportTemplatesCache($this->tempDir, keepInMemory: false),
    'var export & memory' => fn () => new \Keepsuit\Liquid\TemplatesCache\VarExportTemplatesCache($this->tempDir, keepInMemory: true),
    'compiled' => fn () => new \Keepsuit\Liquid\TemplatesCache\CompiledTemplatesCache($this->tempDir, keepInMemory: false),
    'compiled & memory' => fn () => new \Keepsuit\Liquid\TemplatesCache\CompiledTemplatesCache($this->tempDir, keepInMemory: true),
]);

function countingSerializeCache(string $path, bool $keepInMemory): \Keepsuit\Liquid\TemplatesCache\SerializeTemplatesCache
{
    return new class($path, $keepInMemory) extends \Keepsuit\Liquid\TemplatesCache\SerializeTemplatesCache
    {
        public int $loads = 0;

        protected function loadCompiledTemplate(string $compiledPath): ?\Keepsuit\Liquid\Template
        {
            $this->loads++;

            return parent::loadCompiledTemplate($compiledPath);
        }
    };
}

test('filesystem cache keeps templates loaded from disk in memory', function () {
    $writer = countingSerializeCache($this->tempDir, true);
    $writer->clear();
    $writer->set('test', parseSource('Hello {{ name }}'));

    $reader = countingSerializeCache($this->tempDir, true);
    $first = $reader->get('test');
    $second = $reader->get('test');

    expect($second)->toBe($first)
        ->and($reader->loads)->toBe(1)
        ->and($first->render(new \Keepsuit\Liquid\Render\RenderContext(['name' => 'John'])))->toBe('Hello John');

    $reader->remove('test');
    expect($reader->get('test'))->toBeNull();
});

test('filesystem cache without keepInMemory loads from disk on every get', function () {
    $writer = countingSerializeCache($this->tempDir, true);
    $writer->clear();
    $writer->set('test', parseSource('Hello {{ name }}'));

    $reader = countingSerializeCache($this->tempDir, false);
    $reader->get('test');
    $reader->get('test');

    expect($reader->loads)->toBe(2);
});

test('filesystem cache does not memoize missing templates', function () {
    $writer = countingSerializeCache($this->tempDir, true);
    $writer->clear();

    $reader = countingSerializeCache($this->tempDir, true);
    expect($reader->get('missing'))->toBeNull();

    $writer->set('missing', parseSource('Hello'));
    expect($reader->get('missing'))->toBeInstanceOf(\Keepsuit\Liquid\Template::class);
});
