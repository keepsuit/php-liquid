<?php

use Keepsuit\Liquid\Exceptions\InternalException;
use Keepsuit\Liquid\Exceptions\StandardException;
use Keepsuit\Liquid\Exceptions\SyntaxException;
use Keepsuit\Liquid\Parse\ErrorMode;
use Keepsuit\Liquid\Render\Context;
use Keepsuit\Liquid\Support\Arr;
use Keepsuit\Liquid\Template;
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
    $template = Template::parse(' {{ errors.standard_error }} ');

    expect($template->render(new Context(staticEnvironment: ['errors' => new ErrorDrop()])))
        ->toBe(' Liquid error: Standard error ');

    expect($template->getErrors())->toHaveCount(1);
    expect($template->getErrors()[0])->toBeInstanceOf(StandardException::class);
});

test('syntax error', function () {
    $template = Template::parse(' {{ errors.syntax_error }} ');

    expect($template->render(new Context(staticEnvironment: ['errors' => new ErrorDrop()])))
        ->toBe(' Liquid syntax error: Syntax error ');

    expect($template->getErrors())->toHaveCount(1);
    expect($template->getErrors()[0])->toBeInstanceOf(SyntaxException::class);
});

test('argument error', function () {
    $template = Template::parse(' {{ errors.argument_error }} ');

    expect($template->render(new Context(staticEnvironment: ['errors' => new ErrorDrop()])))
        ->toBe(' Liquid error: Argument error ');

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
    expect(fn () => Template::parse('{% if 1 =! 2 %}ok{% endif %}'))->toThrow(SyntaxException::class);
});

test('lax unrecognized operator', function () {
    $template = Template::parse('{% if 1 =! 2 %}ok{% endif %}', errorMode: ErrorMode::Lax);

    expect($template->render(new Context()))->toBe(' Liquid error: Unknown operator =! ');
    expect($template->getErrors())->toHaveCount(1);
    expect($template->getErrors()[0])->toBeInstanceOf(SyntaxException::class);
})->skip('if lax parsing not implemented');

test('with line numbers adds numbers to parser errors', function () {
    assertMatchSyntaxError(
        "Liquid syntax error (line 3): Unknown tag '{% \"cat\" | foobar %}'",
        <<<'LIQUID'
        foobar

        {% "cat" | foobar %}

        bla
        LIQUID
    );
});

test('with line numbers adds numbers to parser errors with whitespace trim', function () {
    assertMatchSyntaxError(
        "Liquid syntax error (line 3): Unknown tag '{%- \"cat\" | foobar -%}'",
        <<<'LIQUID'
        foobar

        {%- "cat" | foobar -%}

        bla
        LIQUID
    );
});

test('parsing warn with line numbers adds numbers to lexer errors', function () {
    $template = Template::parse(
        <<<'LIQUID'

        foobar

        {% if 1 =! 2 %}ok{% endif %}

        bla

        LIQUID,
        errorMode: ErrorMode::Warn,
        lineNumbers: true
    );

    expect(Arr::map($template->getWarnings(), 'message'))
        ->toBe(['Liquid syntax error (line 4): Unexpected character = in "1 =! 2"']);
})->skip('if tag lax parsing not implemented');

test('parsing strict with line numbers adds numbers to lexer errors', function () {
    try {
        Template::parse(
            <<<'LIQUID'

        foobar

        {% if 1 =! 2 %}ok{% endif %}

        bla

        LIQUID,
            errorMode: ErrorMode::Strict,
            lineNumbers: true
        );
    } catch (SyntaxException $exception) {
        expect((string) $exception)
            ->toBe('Liquid syntax error (line 4): Unexpected character = in "1 =! 2"');

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
        'Liquid syntax error (line 1): Unexpected character = in "1 =! 2"',
        ' {% if 1 =! 2 %}ok{% endif %} ',
        errorMode: ErrorMode::Strict,
    );

    assertMatchSyntaxError(
        'Liquid syntax error (line 1): Unexpected character % in "{{%%%}}"',
        '{{%%%}}',
        errorMode: ErrorMode::Strict,
    );
});

test('warnings', function () {
    $template = Template::parse(
        '{% if ~~~ %}{{%%%}}{% else %}{{ hello. }}{% endif %}',
        errorMode: ErrorMode::Warn,
    );

    expect($template->getWarnings())
        ->toHaveCount(3)
        ->{0}->getMessage()->toBe('Unexpected character ~ in "~~~"')
        ->{1}->getMessage()->toBe('Unexpected character % in "{{%%%}}"')
        ->{2}->getMessage()->toBe('Expected id but found end_of_string in "{{ hello. }}"');
})->skip('if tag lax parsing not implemented');

test('warnings with line numbers', function () {
    $template = Template::parse(
        <<<'LIQUID'
        {% if ~~~ %}
        {{%%%}}{% else %}
        {{ hello. }}{% endif %}
        LIQUID,
        errorMode: ErrorMode::Warn,
    );

    expect($template->getWarnings())
        ->toHaveCount(3)
        ->{0}->render()->toBe('Liquid syntax error (line 1): Unexpected character ~ in "~~~"')
        ->{1}->render()->toBe('Liquid syntax error (line 2): Unexpected character % in "{{%%%}}"')
        ->{2}->render()->toBe('Liquid syntax error (line 3): Expected id but found end_of_string in "{{ hello. }}"');
})->skip('if tag lax parsing not implemented');

test('default exception renderer with internal error', function () {
    $template = Template::parse('This is a runtime error: {{ errors.runtime_error }}', lineNumbers: true);

    $output = $template->render(new Context(staticEnvironment: ['errors' => new ErrorDrop()]));

    expect($output)->toBe('This is a runtime error: Liquid error (line 1): Internal exception');
    expect($template->getErrors())
        ->toHaveCount(1)
        ->{0}->toBeInstanceOf(InternalException::class);
});

test('render template name with line numbers', function () {
    $template = Template::parse("Argument error:\n{% render 'product' with errors %}", lineNumbers: true);

    $output = $template->render(new Context(
        staticEnvironment: ['errors' => new ErrorDrop()],
        fileSystem: new StubFileSystem([
            'product' => '{{ errors.argument_error }}',
        ])
    ));

    expect($output)
        ->toBe("Argument error:\nLiquid error (product line 1): Argument error");

    expect($template->getErrors())
        ->toHaveCount(1)
        ->{0}->toBeInstanceOf(\Keepsuit\Liquid\Exceptions\InvalidArgumentException::class);
});

test('error is thrown during parse with template name', function () {
    $template = Template::parse("{% render 'loop' %}", lineNumbers: true);

    $output = $template->render(new Context(
        staticEnvironment: ['errors' => new ErrorDrop()],
        fileSystem: new StubFileSystem([
            'loop' => "{% render 'loop' %}",
        ])
    ));

    expect($output)->toBe('Liquid error (loop line 1): Nesting too deep');
});

test('internal error is thrown with template name', function () {
    $template = Template::parse("{% render 'snippet' with errors %}", lineNumbers: true);

    $output = $template->render(new Context(
        staticEnvironment: ['errors' => new ErrorDrop()],
        fileSystem: new StubFileSystem([
            'snippet' => '{{ errors.runtime_error }}',
        ])
    ));

    expect($output)->toBe('Liquid error (snippet line 1): Internal exception');
});
