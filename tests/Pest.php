<?php

use Keepsuit\Liquid\Exceptions\SyntaxException;
use Keepsuit\Liquid\Template;
use Keepsuit\Liquid\TemplateFactory;
use Keepsuit\Liquid\Tests\Stubs\StubFileSystem;
use PHPUnit\Framework\ExpectationFailedException;

function fixture(string $path): string
{
    return __DIR__.'/fixtures/'.$path;
}

/**
 * @throws SyntaxException
 */
function parseTemplate(
    string $source,
    bool $lineNumbers = true,
    TemplateFactory $factory = new TemplateFactory(),
): Template {
    return $factory
        ->lineNumbers($lineNumbers)
        ->parseString($source);
}

/**
 * @throws SyntaxException
 */
function renderTemplate(
    string $template,
    array $assigns = [],
    array $registers = [],
    array $partials = [],
    bool $renderErrors = false,
    TemplateFactory $factory = new TemplateFactory()
): string {
    $factory = $factory->setFilesystem(new StubFileSystem(partials: $partials))
        ->lineNumbers()
        ->rethrowExceptions(! $renderErrors);

    $template = $factory->parseString($template);

    $context = $factory->newRenderContext(
        staticEnvironment: $assigns,
    );

    foreach ($registers as $key => $value) {
        $context->setRegister($key, $value);
    }

    return $template->render($context);
}

function assertTemplateResult(
    string $expected,
    string $template,
    array $assigns = [],
    array $registers = [],
    array $partials = [],
    bool $renderErrors = false,
    TemplateFactory $factory = new TemplateFactory(),
): void {
    expect(renderTemplate(
        template: $template,
        assigns: $assigns,
        registers: $registers,
        partials: $partials,
        renderErrors: $renderErrors,
        factory: $factory,
    ))->toBe($expected);
}

function assertMatchSyntaxError(
    string $error,
    string $template,
    array $assigns = [],
    array $registers = [],
    array $partials = [],
    TemplateFactory $factory = new TemplateFactory()
): void {
    try {
        renderTemplate(template: $template, assigns: $assigns, registers: $registers, partials: $partials, factory: $factory);
    } catch (SyntaxException $exception) {
        expect($exception->toLiquidErrorMessage())->toBe($error);

        return;
    }

    throw new ExpectationFailedException('Syntax Exception not thrown.');
}
