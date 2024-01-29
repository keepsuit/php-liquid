<?php

use Keepsuit\Liquid\Exceptions\InternalException;
use Keepsuit\Liquid\Exceptions\StackLevelException;
use Keepsuit\Liquid\Exceptions\StandardException;
use Keepsuit\Liquid\Exceptions\SyntaxException;
use Keepsuit\Liquid\Render\RenderContext;
use Keepsuit\Liquid\TemplateFactory;
use Keepsuit\Liquid\Tests\Stubs\ErrorDrop;
use Keepsuit\Liquid\Tests\Stubs\StubFileSystem;

test('template parsed with line numbers renders them in errors', function () {
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

    assertTemplateResult($expected, $template, assigns: ['errors' => new ErrorDrop()], renderErrors: true);
});

test('standard error', function () {
    $template = parseTemplate(' {{ errors.standard_error }} ', lineNumbers: false);

    expect($template->render(new RenderContext(staticEnvironment: ['errors' => new ErrorDrop()])))
        ->toBe(' Liquid error (line 1): Standard error ');

    expect($template->getErrors())->toHaveCount(1);
    expect($template->getErrors()[0])->toBeInstanceOf(StandardException::class);
});

test('syntax error', function () {
    $template = parseTemplate(' {{ errors.syntax_error }} ', lineNumbers: false);

    expect($template->render(new RenderContext(staticEnvironment: ['errors' => new ErrorDrop()])))
        ->toBe(' Liquid syntax error (line 1): Syntax error ');

    expect($template->getErrors())->toHaveCount(1);
    expect($template->getErrors()[0])->toBeInstanceOf(SyntaxException::class);
});

test('argument error', function () {
    $template = parseTemplate(' {{ errors.argument_error }} ', lineNumbers: false);

    expect($template->render(new RenderContext(staticEnvironment: ['errors' => new ErrorDrop()])))
        ->toBe(' Liquid error (line 1): Argument error ');

    expect($template->getErrors())->toHaveCount(1);
    expect($template->getErrors()[0])->toBeInstanceOf(\Keepsuit\Liquid\Exceptions\InvalidArgumentException::class);
});

test('missing endtag parse time error', function () {
    assertMatchSyntaxError(
        "Liquid syntax error (line 1): 'for' tag was never closed",
        ' {% for a in b %} ... '
    );
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
        LIQUID
    );
});

test('with line numbers adds numbers to parser errors with whitespace trim', function () {
    assertMatchSyntaxError(
        'Liquid syntax error (line 3): A block must start with a tag name.',
        <<<'LIQUID'
        foobar

        {%- "cat" | foobar -%}

        bla
        LIQUID
    );
});

test('parsing strict with line numbers adds numbers to lexer errors', function () {
    try {
        parseTemplate(
            <<<'LIQUID'

        foobar

        {% if 1 =! 2 %}ok{% endif %}

        bla

        LIQUID,
            lineNumbers: true
        );
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
        LIQUID
    );
});

test('strict error messages', function () {
    assertMatchSyntaxError(
        'Liquid syntax error (line 1): Unexpected character !',
        ' {% if 1 =! 2 %}ok{% endif %} ',
    );

    assertMatchSyntaxError(
        'Liquid syntax error (line 1): Unexpected character %',
        '{{%%%}}',
    );
});

test('default exception renderer with internal error', function () {
    $template = parseTemplate('This is a runtime error: {{ errors.runtime_error }}', lineNumbers: true);

    $output = $template->render(new RenderContext(staticEnvironment: ['errors' => new ErrorDrop()]));

    expect($output)->toBe('This is a runtime error: Liquid error (line 1): Internal exception');
    expect($template->getErrors())
        ->toHaveCount(1)
        ->{0}->toBeInstanceOf(InternalException::class);
});

test('render template name with line numbers', function () {
    $factory = TemplateFactory::new()
        ->setFilesystem(new StubFileSystem([
            'product' => '{{ errors.argument_error }}',
        ]));

    $template = parseTemplate("Argument error:\n{% render 'product' with errors %}", factory: $factory);

    $output = $template->render($factory->newRenderContext(
        staticEnvironment: ['errors' => new ErrorDrop()],
    ));

    expect($output)
        ->toBe("Argument error:\nLiquid error (product line 1): Argument error");

    expect($template->getErrors())
        ->toHaveCount(1)
        ->{0}->toBeInstanceOf(\Keepsuit\Liquid\Exceptions\InvalidArgumentException::class);
});

test('error is thrown during parse with template name', function () {
    try {
        renderTemplate("{% render 'loop' %}", partials: [
            'loop' => "{% render 'loop' %}",
        ]);
    } catch (StackLevelException $exception) {
        expect($exception->toLiquidErrorMessage())
            ->toBe('Liquid error (loop line 1): Nesting too deep');

        return;
    }

    $this->fail('Expected StackLevelException to be thrown.');
});

test('internal error is thrown with template name', function () {
    $factory = TemplateFactory::new()
        ->setFilesystem(new StubFileSystem([
            'product' => '{{ errors.argument_error }}',
        ]));

    expect(fn () => parseTemplate("{% render 'snippet' with errors %}", factory: $factory))
        ->toThrow(InternalException::class);
});
