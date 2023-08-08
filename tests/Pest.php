<?php

use Keepsuit\Liquid\Context;
use Keepsuit\Liquid\ErrorMode;
use Keepsuit\Liquid\SyntaxException;
use Keepsuit\Liquid\Template;

beforeEach(function () {
    Template::$errorMode = ErrorMode::Strict;
});

function fixture(string $path): string
{
    return __DIR__.'/fixtures/'.$path;
}

function assertTemplateResult(string $expected, string $template, array $assigns = [], array $registers = []): void
{
    $template = Template::parse($template, ['line_numbers' => true]);
    $context = new Context(
        registers: $registers,
        rethrowExceptions: true,
        staticEnvironment: $assigns,
    );
    expect($template->render($context))->toBe($expected);
}

function assertMatchSyntaxError(string $error, string $source): void
{
    expect(fn () => Template::parse($source)->render())
        ->toThrow(SyntaxException::class, $error);
}
