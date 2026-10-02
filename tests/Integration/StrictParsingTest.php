<?php

use Keepsuit\Liquid\Contracts\LiquidErrorHandler;
use Keepsuit\Liquid\EnvironmentFactory;
use Keepsuit\Liquid\Exceptions\LiquidException;
use Keepsuit\Liquid\Exceptions\StandardException;
use Keepsuit\Liquid\Exceptions\SyntaxException;
use Keepsuit\Liquid\Render\RenderContextOptions;
use Keepsuit\Liquid\Tests\Stubs\StubFileSystem;

test('invalid syntax fails during parsing regardless of render options', function (string $source, bool $strictVariables, bool $strictFilters, bool $rethrowErrors, bool $lazyParsing) {
    $environment = EnvironmentFactory::new()
        ->setStrictVariables($strictVariables)
        ->setStrictFilters($strictFilters)
        ->setRethrowErrors($rethrowErrors)
        ->setLazyParsing($lazyParsing)
        ->build();

    expect(fn () => $environment->parseString($source))->toThrow(SyntaxException::class);
})->with([
    'empty if' => ['{% if %}Y{% endif %}'],
    'missing or condition' => ['{% if n == 5 or %}Y{% endif %}'],
    'empty elsif' => ['{% if n == 5 %}Y{% elsif %}Z{% endif %}'],
    'missing contains operand' => ['{% if n contains %}Y{% endif %}'],
    'empty unless' => ['{% unless %}Y{% endunless %}'],
    'missing and condition' => ['{% unless n and %}Y{% endunless %}'],
    'missing comparison operand' => ['{% if n == %}Y{% endif %}'],
    'empty tag' => ['{%%}'],
    'whitespace tag' => ['{% %}'],
    'trimmed empty tag' => ['{%- -%}'],
    'unclosed trimmed raw' => ['{%- raw -%} {{ invalid | }}'],
    'raw with arguments' => ['{% raw argument %}body{% endraw %}'],
    'malformed tablerow attribute' => ['{% tablerow i in items cols 2 %}x{% endtablerow %}'],
    'duplicate for else' => ['{% for i in arr %}{% else %}{% else %}{% endfor %}'],
    'missing filter argument' => ['{{ n | plus: }}'],
    'missing argument before pipe' => ['{{ n | plus: | minus: 1 }}'],
    'missing named argument' => ['{{ n | custom: key: }}'],
    'missing argument after comma' => ['{{ n | plus: 1, }}'],
    'quoted capture target' => ["{% capture 'x' %}Y{% endcapture %}"],
    'quoted increment target' => ["{% increment 'x' %}"],
    'render without named argument colon' => ["{% render 'p' n %}"],
    'assign indexed target' => ['{% assign a[0] = 1 %}'],
])->with([false, true])->with([false, true])->with([false, true])->with([false, true]);

test('complete syntax and explicit nil expressions remain valid', function (bool $strictVariables, bool $strictFilters, bool $rethrowErrors, bool $lazyParsing, bool $stream) {
    $environment = EnvironmentFactory::new()
        ->setStrictVariables($strictVariables)
        ->setStrictFilters($strictFilters)
        ->setRethrowErrors($rethrowErrors)
        ->setLazyParsing($lazyParsing)
        ->build();
    $template = $environment->parseString(<<<'LIQUID'
        {% if nil %}N{% elsif n == nil or n == 5 and true %}Y{% endif %}|{% unless null %}U{% endunless %}|{{ n | plus: nil }}|{{ n | plus: null }}|{{ n | abs }}|{% for i in arr %}{% for j in nil %}N{% else %}{{ i }}{% endfor %}{% else %}E{% endfor %}|{% raw %}{% %}{% endraw %}{% comment %}{%- -%}{% endcomment %}{% # inline comment %}
        LIQUID);
    $context = $environment->newRenderContext(data: ['n' => 5, 'arr' => [1, 2]]);

    expect($stream ? implode('', iterator_to_array($template->stream($context))) : $template->render($context))
        ->toBe('Y|U|5|5|5|12|{% %}');
    expect($context->getErrors())->toBe([]);
})->with([false, true])->with([false, true])->with([false, true])->with([false, true])->with([false, true]);

test('named filter arguments accept explicit nil', function () {
    expect(fn () => EnvironmentFactory::new()->build()->parseString('{{ n | custom: key: nil }}'))
        ->not->toThrow(SyntaxException::class);
});

test('invalid literal partials fail during parsing even when render errors are handled', function (bool $lazyParsing) {
    $handler = new class implements LiquidErrorHandler
    {
        public function handle(LiquidException $error): string
        {
            throw new LogicException('Parse errors must not reach the render error handler.');
        }
    };
    $environment = EnvironmentFactory::new()
        ->setErrorHandler($handler)
        ->setRethrowErrors(false)
        ->setLazyParsing($lazyParsing)
        ->setFilesystem(new StubFileSystem(['p' => '{% if %}Y{% endif %}']))
        ->build();

    expect(fn () => $environment->parseString("{% render 'p' %}"))->toThrow(SyntaxException::class);
    expect($environment->templatesCache->has('p'))->toBeFalse();
})->with([false, true]);

test('partial parsing during rendering respects context overrides and error handlers', function (bool $lazyParsing, bool $rethrowErrors, bool $customHandler, bool $stream) {
    $handler = new class implements LiquidErrorHandler
    {
        public function handle(LiquidException $error): string
        {
            return '[handled]';
        }
    };
    $factory = EnvironmentFactory::new()
        ->setLazyParsing(! $lazyParsing)
        ->setRethrowErrors(! $rethrowErrors)
        ->setFilesystem(new StubFileSystem(['p' => '{{ n | plus: }}']));
    if ($customHandler) {
        $factory->setErrorHandler($handler);
    }
    $environment = $factory->build();
    $environment->templatesCache->set('p', $environment->parseString('valid'));
    $template = $environment->parseString("A{% render 'p' %}B");
    $environment->templatesCache->remove('p');
    $context = $environment->newRenderContext(options: new RenderContextOptions(
        strictVariables: true, strictFilters: true, rethrowErrors: $rethrowErrors, lazyParsing: $lazyParsing,
    ));
    $render = fn () => $stream ? implode('', iterator_to_array($template->stream($context))) : $template->render($context);

    if ($rethrowErrors) {
        expect($render)->toThrow($lazyParsing ? SyntaxException::class : StandardException::class);
    } elseif ($customHandler) {
        expect($render())->toBe('A[handled]B');
    } else {
        expect($render())->toStartWith('ALiquid ')->toEndWith('B');
    }
    expect($context->getErrors())->toHaveCount(1);
    expect($context->getErrors()[0])->toBeInstanceOf($lazyParsing ? SyntaxException::class : StandardException::class);
    expect($environment->templatesCache->has('p'))->toBeFalse();
})->with([false, true])->with([false, true])->with([false, true])->with([false, true]);
