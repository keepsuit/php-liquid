<?php

use Keepsuit\Liquid\Exceptions\ArithmeticException;
use Keepsuit\Liquid\Exceptions\InternalException;
use Keepsuit\Liquid\Exceptions\StackLevelException;
use Keepsuit\Liquid\Exceptions\StandardException;
use Keepsuit\Liquid\Exceptions\SyntaxException;
use Keepsuit\Liquid\Render\RenderContext;
use Keepsuit\Liquid\Tests\Stubs\ErrorDrop;
use Keepsuit\Liquid\Tests\Stubs\StubFileSystem;

describe('rendering with template backends 1', function () {
    test('template parsed with line numbers renders them in errors', function (bool $compiled) {
        $template = <<<'LIQUID'
        Hello,

        {{ errors.standard_error }} will raise a standard error.

        Bla bla test.

        {{ errors.syntax_error }} will raise a syntax error.

        This is an argument error: {{ errors.argument_error }}

        Bla.
        LIQUID;

        $expected = <<<'HTML'
        Hello,

        Liquid error (line 3): Standard error will raise a standard error.

        Bla bla test.

        Liquid syntax error (line 7): Syntax error will raise a syntax error.

        This is an argument error: Liquid error (line 9): Argument error

        Bla.
        HTML;

        assertTemplateResult($expected, $template, staticData: ['errors' => new ErrorDrop], renderErrors: true, compiled: $compiled);
    });

    test('standard error', function (bool $compiled) {
        $template = parseTemplate(' {{ errors.standard_error }} ', compiled: $compiled);

        expect($template->render(new RenderContext(staticData: ['errors' => new ErrorDrop])))
            ->toBe(' Liquid error (line 1): Standard error ');

        expect($template->getErrors())->toHaveCount(1);
        expect($template->getErrors()[0])->toBeInstanceOf(StandardException::class);
    });

    test('syntax error', function (bool $compiled) {
        $template = parseTemplate(' {{ errors.syntax_error }} ', compiled: $compiled);

        expect($template->render(new RenderContext(staticData: ['errors' => new ErrorDrop])))
            ->toBe(' Liquid syntax error (line 1): Syntax error ');

        expect($template->getErrors())->toHaveCount(1);
        expect($template->getErrors()[0])->toBeInstanceOf(SyntaxException::class);
    });

    test('argument error', function (bool $compiled) {
        $template = parseTemplate(' {{ errors.argument_error }} ', compiled: $compiled);

        expect($template->render(new RenderContext(staticData: ['errors' => new ErrorDrop])))
            ->toBe(' Liquid error (line 1): Argument error ');

        expect($template->getErrors())->toHaveCount(1);
        expect($template->getErrors()[0])->toBeInstanceOf(\Keepsuit\Liquid\Exceptions\InvalidArgumentException::class);
    });
})->with('template backends');

test('missing endtag parse time error', function () {
    assertMatchSyntaxError(
        "Liquid syntax error (line 1): 'for' tag was never closed",
        ' {% for a in b %} ... ');
});

test('unrecognized operator', function () {
    expect(fn () => parseTemplate('{% if 1 =! 2 %}ok{% endif %}'))->toThrow(SyntaxException::class);
});

test('with line numbers adds numbers to parser errors', function () {
    assertMatchSyntaxError(
        'Liquid syntax error (line 3): A block must start with a tag name.',
        <<<'LIQUID'
        foobar

        {% "cat" | foobar %}

        bla
        LIQUID);
});

test('with line numbers adds numbers to parser errors with whitespace trim', function () {
    assertMatchSyntaxError(
        'Liquid syntax error (line 3): A block must start with a tag name.',
        <<<'LIQUID'
        foobar

        {%- "cat" | foobar -%}

        bla
        LIQUID);
});

test('parsing strict with line numbers adds numbers to lexer errors', function () {
    try {
        parseTemplate(
            <<<'LIQUID'

        foobar

        {% if 1 =! 2 %}ok{% endif %}

        bla

        LIQUID);
    } catch (SyntaxException $exception) {
        expect($exception->toLiquidErrorMessage())
            ->toBe('Liquid syntax error (line 4): Unexpected character !');

        return;
    }

    $this->fail('Expected SyntaxException to be thrown.');
});

test('syntax errors in nested blocks have correct line number', function () {
    assertMatchSyntaxError(
        "Liquid syntax error (line 4): Unknown tag 'foo'",
        <<<'LIQUID'
        foobar

        {% if 1 != 2 %}
            {% foo %}
        {% endif %}

        bla
        LIQUID);
});

test('strict error messages', function () {
    assertMatchSyntaxError(
        'Liquid syntax error (line 1): Unexpected character !',
        ' {% if 1 =! 2 %}ok{% endif %} ');

    assertMatchSyntaxError(
        'Liquid syntax error (line 1): Unexpected character %',
        '{{%%%}}');
});

describe('rendering with template backends 2', function () {
    test('default exception renderer with internal error', function (bool $compiled) {
        $template = parseTemplate('This is a runtime error: {{ errors.runtime_error }}', compiled: $compiled);

        $output = $template->render(new RenderContext(staticData: ['errors' => new ErrorDrop]));

        expect($output)->toBe('This is a runtime error: Liquid error (line 1): Internal exception');
        expect($template->getErrors())
            ->toHaveCount(1)
            ->{0}->toBeInstanceOf(InternalException::class);
    });

    test('render template name with line numbers', function (bool $compiled) {
        $environment = testEnvironmentFactory($compiled)
            ->setFilesystem(new StubFileSystem([
                'product' => '{{ errors.argument_error }}',
            ]))->build();

        $template = testParseString($environment, "Argument error:\n{% render 'product' with errors %}");

        $output = $template->render($environment->newRenderContext(
            staticData: ['errors' => new ErrorDrop],
        ));

        expect($output)
            ->toBe("Argument error:\nLiquid error (product line 1): Argument error");

        expect($template->getErrors())
            ->toHaveCount(1)
            ->{0}->toBeInstanceOf(\Keepsuit\Liquid\Exceptions\InvalidArgumentException::class);
    });

    test('error is thrown during parse with template name', function (bool $compiled) {
        try {
            renderTemplate("{% render 'loop' %}", partials: [
                'loop' => "{% render 'loop' %}",
            ], compiled: $compiled);
        } catch (StackLevelException $exception) {
            expect($exception->toLiquidErrorMessage())
                ->toBe('Liquid error (loop line 1): Nesting too deep');

            return;
        }

        $this->fail('Expected StackLevelException to be thrown.');
    });

    test('internal error is thrown with template name', function (bool $compiled) {
        $environment = testEnvironmentFactory($compiled)
            ->setFilesystem(new StubFileSystem([
                'product' => '{{ errors.argument_error }}',
            ]))->build();

        expect(fn () => testParseString($environment, "{% render 'snippet' with errors %}"))
            ->toThrow(InternalException::class);
    });

    test('arithmetic errors render only the short message', function (bool $compiled, string $template) {
        $output = renderTemplate($template, renderErrors: true, compiled: $compiled);

        expect($output)
            ->toBe('Liquid error (line 1): divided by 0')
            ->not->toContain('Stack trace')
            ->not->toContain('.php');
    })->with([
        '{{ 1 | divided_by: 0 }}',
        '{{ 1 | modulo: 0 }}',
    ]);

    test('arithmetic errors are thrown with the short message when rethrowing errors', function (bool $compiled) {
        try {
            renderTemplate('{{ 1 | divided_by: 0 }}', compiled: $compiled);
        } catch (ArithmeticException $exception) {
            expect($exception->toLiquidErrorMessage())->toBe('Liquid error (line 1): divided by 0');
            expect($exception->getPrevious())->toBeInstanceOf(DivisionByZeroError::class);

            return;
        }

        $this->fail('Expected ArithmeticException to be thrown.');
    });
})->with('template backends');
