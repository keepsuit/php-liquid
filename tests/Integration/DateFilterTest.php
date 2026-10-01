<?php

use Keepsuit\Liquid\Contracts\LiquidErrorHandler;
use Keepsuit\Liquid\EnvironmentFactory;
use Keepsuit\Liquid\Exceptions\InvalidArgumentException;
use Keepsuit\Liquid\Exceptions\LiquidException;
use Keepsuit\Liquid\Exceptions\UndefinedVariableException;
use Keepsuit\Liquid\Render\RenderContextOptions;

test('date fallback does not report errors under render options', function (bool $strictVariables, bool $strictFilters, bool $rethrowErrors) {
    $handler = new class implements LiquidErrorHandler
    {
        public function handle(LiquidException $error): string
        {
            throw new LogicException('Date fallback must not call the error handler');
        }
    };
    $environment = EnvironmentFactory::new()
        ->setStrictVariables($strictVariables)
        ->setStrictFilters($strictFilters)
        ->setRethrowErrors($rethrowErrors)
        ->setErrorHandler($handler)
        ->build();
    $template = $environment->parseString("{{ input | date: '%Y' }}");

    foreach (['garbage' => 'garbage', '2024-13-45' => '2024-13-45'] as $input => $expected) {
        expect($template->render($environment->newRenderContext(data: ['input' => $input])))->toBe($expected);
        expect($template->getErrors())->toBeEmpty();
    }
    foreach ([[3.7, '3.7'], [true, 'true'], [false, 'false'], [null, '']] as [$input, $expected]) {
        expect($template->render($environment->newRenderContext(data: ['input' => $input])))->toBe($expected);
        expect($template->getErrors())->toBeEmpty();
    }
})->with([
    [false, false, false], [false, false, true],
    [false, true, false], [false, true, true],
    [true, false, false], [true, false, true],
    [true, true, false], [true, true, true],
]);

test('date preserves strict undefined variable errors', function () {
    $environment = EnvironmentFactory::new()->setStrictVariables(true)->setRethrowErrors(true)->build();
    $template = $environment->parseString("{{ missing | date: '%Y' }}");

    expect(fn () => $template->render($environment->newRenderContext()))->toThrow(UndefinedVariableException::class);
    expect($template->getErrors())->toHaveCount(1);

    $context = $environment->newRenderContext(options: new RenderContextOptions(strictVariables: false));
    expect($template->render($context))->toBe('');
    expect($template->getErrors())->toBeEmpty();
});

test('date format errors use the default handler', function () {
    $environment = EnvironmentFactory::new()->build();
    $template = $environment->parseString("{{ '2024-03-05' | date: '%' }}");

    expect($template->render($environment->newRenderContext()))->toBe('Liquid error (line 1): Invalid date format');
    expect($template->getErrors())->toHaveCount(1);
    expect($template->getErrors()[0])->toBeInstanceOf(InvalidArgumentException::class);
});

test('date format errors use custom handlers and per-context rethrow options', function () {
    $handler = new class implements LiquidErrorHandler
    {
        public int $calls = 0;

        public function handle(LiquidException $error): string
        {
            $this->calls++;

            return '[invalid date]';
        }
    };
    $environment = EnvironmentFactory::new()->setErrorHandler($handler)->build();
    $template = $environment->parseString("{{ '2024-03-05' | date: '%' }}");

    expect($template->render($environment->newRenderContext()))->toBe('[invalid date]');
    expect($template->getErrors())->toHaveCount(1);
    expect($handler->calls)->toBe(1);

    $context = $environment->newRenderContext(options: new RenderContextOptions(rethrowErrors: true));
    expect(fn () => $template->render($context))->toThrow(InvalidArgumentException::class);
    expect($template->getErrors())->toHaveCount(1);
    expect($handler->calls)->toBe(1);
});
