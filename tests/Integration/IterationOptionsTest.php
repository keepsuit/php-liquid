<?php

use Keepsuit\Liquid\Contracts\LiquidErrorHandler;
use Keepsuit\Liquid\EnvironmentFactory;
use Keepsuit\Liquid\Exceptions\InvalidArgumentException;
use Keepsuit\Liquid\Exceptions\LiquidException;
use Keepsuit\Liquid\Exceptions\ResourceLimitException;
use Keepsuit\Liquid\Exceptions\SyntaxException;
use Keepsuit\Liquid\Exceptions\UndefinedFilterException;
use Keepsuit\Liquid\Exceptions\UndefinedVariableException;
use Keepsuit\Liquid\Render\RenderContextOptions;
use Keepsuit\Liquid\Render\ResourceLimits;
use Keepsuit\Liquid\Tests\Stubs\StubFileSystem;

test('iteration behaviour is independent of render options', function (bool $strictVariables, bool $strictFilters, bool $rethrowErrors, bool $lazyParsing, bool $stream) {
    $environment = testEnvironment(EnvironmentFactory::new()
        ->setStrictVariables($strictVariables)
        ->setStrictFilters($strictFilters)
        ->setRethrowErrors($rethrowErrors)
        ->setLazyParsing($lazyParsing)
        ->setFilesystem(new StubFileSystem(['p' => '{{ i }}']))
        ->build());

    $template = $environment->parseString(<<<'LIQUID'
        {% for i in items offset:continue limit:2 %}{{ i }}{% endfor %}|{{ (1..3) | join: ',' }}|{{ (1..3) | size }}|{{ (1..3) | reverse | join }}|{% assign x = (1..3) %}{{ x | join }}|{% for i in (3..1) %}{{ i }}{% else %}E{% endfor %}|{% render 'p' for (1..3) as i %}|{% for i in s %}[{{ i }}]{% endfor %}|{% for i in empty_string %}x{% else %}E{% endfor %}|{% for i in number %}x{% else %}E{% endfor %}|{% for i in nil %}x{% else %}E{% endfor %}|{% tablerow i in nil %}x{% endtablerow %}|{% increment a %}{% increment a %}{{ a }}|{% increment b %} {% decrement b %}|{% cycle n, 'b' %}|{% for i in arr %}{{ forloop.name }}{% endfor %}
        LIQUID);
    $context = $environment->newRenderContext(data: [
        'items' => [1, 2, 3], 's' => 'abc', 'empty_string' => '', 'number' => 5, 'n' => 5, 'arr' => [1],
    ]);

    set_error_handler(function (int $severity, string $message, string $file, int $line): never {
        throw new ErrorException($message, 0, $severity, $file, $line);
    });
    try {
        $output = $stream ? implode('', iterator_to_array($template->stream($context))) : $template->render($context);
    } finally {
        restore_error_handler();
    }

    expect($output)->toBe('12|1,2,3|3|3 2 1|1 2 3|E|123|[abc]|E|E|E||012|0 0|5|i-arr');
    expect($context->getErrors())->toBe([]);
})->with([false, true])->with([false, true])->with([false, true])->with([false, true])->with([false, true]);

test('strict iteration errors are collected or rethrown', function (string $source, bool $rethrowErrors, bool $stream) {
    $environment = testEnvironment(EnvironmentFactory::new()->setStrictVariables(true)->setRethrowErrors($rethrowErrors)->build());
    $template = $environment->parseString($source);
    $context = $environment->newRenderContext();
    $render = fn () => $stream ? implode('', iterator_to_array($template->stream($context))) : $template->render($context);

    if ($rethrowErrors) {
        expect($render)->toThrow(UndefinedVariableException::class);
    } else {
        expect($render())->toBe('AB');
    }
    expect($context->getErrors())->toHaveCount(1);
    expect($context->getErrors()[0])->toBeInstanceOf(UndefinedVariableException::class);
})->with([
    'for' => ['A{% for i in missing %}x{% endfor %}B'],
    'tablerow' => ['A{% tablerow i in missing %}x{% endtablerow %}B'],
    'cycle value' => ['A{% cycle missing %}B'],
    'cycle name' => ["A{% cycle missing: 'x', 'y' %}B"],
])->with([false, true])->with([false, true]);

test('iteration errors use the configured handler unless rethrow is enabled', function (bool $rethrowErrors, bool $stream) {
    $handler = new class implements LiquidErrorHandler
    {
        public int $calls = 0;

        public function handle(LiquidException $error): string
        {
            $this->calls++;

            return '[handled]';
        }
    };
    $environment = testEnvironment(EnvironmentFactory::new()->setErrorHandler($handler)->setRethrowErrors($rethrowErrors)->build());
    $template = $environment->parseString("A{% for i in (1..3) offset:'bad' %}{{ i }}{% endfor %}B");
    $context = $environment->newRenderContext();
    $render = fn () => $stream ? implode('', iterator_to_array($template->stream($context))) : $template->render($context);

    if ($rethrowErrors) {
        expect($render)->toThrow(InvalidArgumentException::class);
        expect($handler->calls)->toBe(0);
    } else {
        expect($render())->toBe('A[handled]B');
        expect($handler->calls)->toBe(1);
    }
    expect($context->getErrors())->toHaveCount(1);
})->with([false, true])->with([false, true]);

test('context overrides are inherited by range render partials', function (bool $strictFilters, bool $stream) {
    $environment = testEnvironment(EnvironmentFactory::new()
        ->setFilesystem(new StubFileSystem(['p' => '{{ i | unknown_filter }}']))
        ->build());
    $template = $environment->parseString("{% render 'p' for (1..3) as i %}");
    $context = $environment->newRenderContext(options: new RenderContextOptions(
        strictVariables: true, strictFilters: $strictFilters, rethrowErrors: true, lazyParsing: false,
    ));
    $render = fn () => $stream ? implode('', iterator_to_array($template->stream($context))) : $template->render($context);

    if ($strictFilters) {
        expect($render)->toThrow(UndefinedFilterException::class);
        expect($context->getErrors()[0])->toBeInstanceOf(UndefinedFilterException::class);
    } else {
        expect($render())->toBe('123');
        expect($context->getErrors())->toBe([]);
    }
})->with([false, true])->with([false, true]);

test('range render partials share cumulative assignment limits', function (bool $stream) {
    $environment = testEnvironment(EnvironmentFactory::new()
        ->setFilesystem(new StubFileSystem(['p' => '{% assign values = (1..3) %}{{ i }}']))
        ->setLazyParsing(false)
        ->build());
    $template = $environment->parseString("{% render 'p' for (1..3) as i %}");
    $limitedContext = $environment->newRenderContext(resourceLimits: new ResourceLimits(cumulativeAssignScoreLimit: 11));
    $render = fn () => $stream ? implode('', iterator_to_array($template->stream($limitedContext))) : $template->render($limitedContext);
    expect($render)->toThrow(ResourceLimitException::class);

    $context = $environment->newRenderContext(resourceLimits: new ResourceLimits(cumulativeAssignScoreLimit: 12));
    expect($stream ? implode('', iterator_to_array($template->stream($context))) : $template->render($context))->toBe('123');
    expect($context->resourceLimits->getCumulativeAssignScore())->toBe(12);
})->with([false, true]);

test('iteration syntax remains strict regardless of render options', function (string $source, bool $strictVariables) {
    $environment = testEnvironment(EnvironmentFactory::new()->setStrictVariables($strictVariables)->setRethrowErrors(false)->build());
    expect(fn () => $environment->parseString($source))->toThrow(SyntaxException::class);
})->with([
    'missing collection' => ['{% for i in %}x{% endfor %}'],
    'invalid for attribute' => ['{% for i in nil unexpected:1 %}x{% endfor %}'],
    'cycle trailing tokens' => ["{% cycle n, 'b' extra %}"],
    'cycle missing value' => ["{% cycle 'group': %}"],
    'cycle trailing comma' => ['{% cycle n, %}'],
    'increment dotted target' => ['{% increment x.y %}'],
])->with([false, true]);
