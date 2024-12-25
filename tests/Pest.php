<?php

use Keepsuit\Liquid\Environment;
use Keepsuit\Liquid\EnvironmentFactory;
use Keepsuit\Liquid\Exceptions\SyntaxException;
use Keepsuit\Liquid\Parse\ParseContext;
use Keepsuit\Liquid\Parse\TokenStream;
use Keepsuit\Liquid\Template;
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
    ?Environment $environment = null,
): Template {
    return ($environment ?? Environment::default())->parseString($source);
}

function buildRenderContext(
    array $assigns = [],
    array $registers = [],
    ?Environment $environment = null
) {
    $context = ($environment ?? Environment::default())->newRenderContext(
        staticData: $assigns,
    );

    foreach ($registers as $key => $value) {
        $context->setRegister($key, $value);
    }

    return $context;
}

/**
 * @throws \Keepsuit\Liquid\Exceptions\LiquidException
 */
function renderTemplate(
    string $template,
    array $assigns = [],
    array $registers = [],
    array $partials = [],
    bool $renderErrors = false,
    bool $strictVariables = false,
    EnvironmentFactory $factory = new EnvironmentFactory
): string {
    $environment = $factory
        ->setFilesystem(new StubFileSystem(partials: $partials))
        ->setStrictVariables($strictVariables)
        ->setRethrowExceptions(! $renderErrors)
        ->build();

    $template = $environment->parseString($template);

    $context = buildRenderContext(
        assigns: $assigns,
        registers: $registers,
        environment: $environment,
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
    EnvironmentFactory $factory = new EnvironmentFactory
): Generator {
    $environment = $factory
        ->setFilesystem(new StubFileSystem(partials: $partials))
        ->setStrictVariables($strictVariables)
        ->setRethrowExceptions(! $renderErrors)
        ->build();

    $template = $environment->parseString($template);

    $context = buildRenderContext(
        assigns: $assigns,
        registers: $registers,
        environment: $environment,
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
    EnvironmentFactory $factory = new EnvironmentFactory
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
): void {
    try {
        renderTemplate(template: $template, assigns: $assigns, registers: $registers, partials: $partials);
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
