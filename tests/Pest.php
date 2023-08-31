<?php

use Keepsuit\Liquid\Exceptions\SyntaxException;
use Keepsuit\Liquid\Render\Context;
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
    TemplateFactory $factory = null,
): Template {
    return ($factory ?? TemplateFactory::new())->parse($source, lineNumbers: $lineNumbers);
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
    TemplateFactory $factory = null,
): string {
    $template = parseTemplate($template, factory: $factory);

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
    bool $renderErrors = false,
    TemplateFactory $factory = null,
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

function assertMatchSyntaxError(string $error, string $template, array $assigns = [], array $registers = []): void
{
    try {
        renderTemplate(template: $template, assigns: $assigns, registers: $registers);
    } catch (SyntaxException $exception) {
        expect((string) $exception)->toBe($error);

        return;
    }

    throw new ExpectationFailedException('Syntax Exception not thrown.');
}
