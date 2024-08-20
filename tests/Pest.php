<?php

use Keepsuit\Liquid\Exceptions\SyntaxException;
use Keepsuit\Liquid\Parse\ParseContext;
use Keepsuit\Liquid\Parse\TokenStream;
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
    TemplateFactory $factory = new TemplateFactory,
): Template {
    return $factory
        ->parseString($source);
}

function buildRenderContext(
    array $assigns = [],
    array $registers = [],
    TemplateFactory $factory = new TemplateFactory
) {
    $context = $factory->newRenderContext(
        staticEnvironment: $assigns,
    );

    foreach ($registers as $key => $value) {
        $context->setRegister($key, $value);
    }

    return $context;
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
    bool $strictVariables = false,
    TemplateFactory $factory = new TemplateFactory
): string {
    $factory = $factory
        ->setFilesystem(new StubFileSystem(partials: $partials))
        ->setRethrowExceptions(! $renderErrors)
        ->setStrictVariables($strictVariables);

    $template = $factory->parseString($template);

    $context = buildRenderContext(
        assigns: $assigns,
        registers: $registers,
        factory: $factory,
    );

    return $template->render($context);
}

/**
 * @return Generator<string>
 *
 * @throws SyntaxException
 */
function streamTemplate(
    string $template,
    array $assigns = [],
    array $registers = [],
    array $partials = [],
    bool $renderErrors = false,
    bool $strictVariables = false,
    TemplateFactory $factory = new TemplateFactory
): Generator {
    $factory = $factory
        ->setFilesystem(new StubFileSystem(partials: $partials))
        ->setRethrowExceptions(! $renderErrors)
        ->setStrictVariables($strictVariables);

    $template = $factory->parseString($template);

    $context = buildRenderContext(
        assigns: $assigns,
        registers: $registers,
        factory: $factory,
    );

    return $template->stream($context);
}

function assertTemplateResult(
    string $expected,
    string $template,
    array $assigns = [],
    array $registers = [],
    array $partials = [],
    bool $renderErrors = false,
    bool $strictVariables = false,
    TemplateFactory $factory = new TemplateFactory,
): void {
    expect(renderTemplate(
        template: $template,
        assigns: $assigns,
        registers: $registers,
        partials: $partials,
        renderErrors: $renderErrors,
        strictVariables: $strictVariables,
        factory: $factory,
    ))->toBe($expected);
}

function assertMatchSyntaxError(
    string $error,
    string $template,
    array $assigns = [],
    array $registers = [],
    array $partials = [],
    TemplateFactory $factory = new TemplateFactory
): void {
    try {
        renderTemplate(template: $template, assigns: $assigns, registers: $registers, partials: $partials, factory: $factory);
    } catch (SyntaxException $exception) {
        expect($exception->toLiquidErrorMessage())->toBe($error);

        return;
    }

    throw new ExpectationFailedException('Syntax Exception not thrown.');
}

function tokenize(string $source): TokenStream
{
    return (new ParseContext)->tokenize($source);
}

function parse(string|TokenStream $source)
{
    return (new ParseContext)->parse($source instanceof TokenStream ? $source : tokenize($source));
}
