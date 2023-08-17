<?php

use Keepsuit\Liquid\Context;
use Keepsuit\Liquid\ErrorMode;
use Keepsuit\Liquid\SyntaxException;
use Keepsuit\Liquid\Template;
use PHPUnit\Framework\ExpectationFailedException;

uses()->beforeEach(function () {
    Template::$errorMode = ErrorMode::Strict;
})->in(__DIR__);

function fixture(string $path): string
{
    return __DIR__.'/fixtures/'.$path;
}

function parseTemplate(string $template, array $assigns = [], array $registers = [], ErrorMode $errorMode = null): string
{
    $template = Template::parse($template, lineNumbers: true, errorMode: $errorMode);
    $context = new Context(
        registers: $registers,
        rethrowExceptions: true,
        staticEnvironment: $assigns,
    );

    return $template->render($context);
}

function assertTemplateResult(string $expected, string $template, array $assigns = [], array $registers = [], ErrorMode $errorMode = null): void
{
    expect(parseTemplate($template, $assigns, $registers, $errorMode))->toBe($expected);
}

function assertMatchSyntaxError(string $error, string $source): void
{
    try {
        Template::parse($source, lineNumbers: true)->render(new Context());
    } catch (SyntaxException $exception) {
        expect((string) $exception)->toBe($error);

        return;
    }

    throw new ExpectationFailedException('Syntax Exception not thrown.');
}
