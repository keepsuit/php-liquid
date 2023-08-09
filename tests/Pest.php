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

function parseTemplate(string $template, array $assigns = [], array $registers = []): string
{
    $template = Template::parse($template, ['line_numbers' => true]);
    $context = new Context(
        registers: $registers,
        rethrowExceptions: true,
        staticEnvironment: $assigns,
    );

    return $template->render($context);
}

function assertTemplateResult(string $expected, string $template, array $assigns = [], array $registers = []): void
{
    expect(parseTemplate($template, $assigns, $registers))->toBe($expected);
}

function assertMatchSyntaxError(string $error, string $source): void
{
    expect(fn () => Template::parse($source)->render(new Context()))
        ->toThrow(SyntaxException::class, $error);
}
