<?php

use Keepsuit\Liquid\Context;
use Keepsuit\Liquid\ErrorMode;
use Keepsuit\Liquid\Exceptions\SyntaxException;
use Keepsuit\Liquid\Template;
use Keepsuit\Liquid\Tests\Stubs\StubFileSystem;
use PHPUnit\Framework\ExpectationFailedException;

uses()->beforeEach(function () {
    Template::$errorMode = ErrorMode::Strict;
})->in(__DIR__);

function fixture(string $path): string
{
    return __DIR__.'/fixtures/'.$path;
}

/**
 * @throws SyntaxException
 */
function renderTemplate(
    string $template,
    array $assigns = [],
    array $registers = [],
    array $partials = [],
    ErrorMode $errorMode = null,
    bool $renderErrors = false,
): string {
    $template = Template::parse($template, lineNumbers: true, errorMode: $errorMode);

    $fileSystem = new StubFileSystem(partials: $partials);

    $context = new Context(
        staticEnvironment: $assigns,
        rethrowExceptions: ! $renderErrors,
        fileSystem: $fileSystem,
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
    ErrorMode $errorMode = null,
    bool $renderErrors = false,
): void {
    expect(renderTemplate(
        template: $template,
        assigns: $assigns,
        registers: $registers,
        partials: $partials,
        errorMode: $errorMode,
        renderErrors: $renderErrors
    ))->toBe($expected);
}

function assertMatchSyntaxError(string $error, string $template, array $assigns = [], array $registers = [], ErrorMode $errorMode = null): void
{
    try {
        renderTemplate(template: $template, assigns: $assigns, registers: $registers, errorMode: $errorMode);
    } catch (SyntaxException $exception) {
        expect((string) $exception)->toBe($error);

        return;
    }

    throw new ExpectationFailedException('Syntax Exception not thrown.');
}
