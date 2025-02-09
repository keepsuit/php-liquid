<?php

use Keepsuit\Liquid\Contracts\LiquidTemplatesCache;

test('templates cache', function (LiquidTemplatesCache $cache) {
    $cache->clear();

    expect($cache)
        ->has('test')->toBe(false)
        ->get('test')->toBeNull();

    $template = parseTemplate('Hello {{ name }}');

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
    'serialize' => fn () => new \Keepsuit\Liquid\TemplatesCache\SerializeTemplatesCache(__DIR__.'/../cache/serialize', keepInMemory: false),
    'serialize & memory' => fn () => new \Keepsuit\Liquid\TemplatesCache\SerializeTemplatesCache(__DIR__.'/../cache/serialize', keepInMemory: true),
    'var export' => fn () => new \Keepsuit\Liquid\TemplatesCache\VarExportTemplatesCache(__DIR__.'/../cache/var_export', keepInMemory: false),
    'var export & memory' => fn () => new \Keepsuit\Liquid\TemplatesCache\VarExportTemplatesCache(__DIR__.'/../cache/var_export', keepInMemory: true),
]);
