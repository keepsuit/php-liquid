<?php

test('template can be streamed', function () {
    $stream = streamTemplate(<<<'LIQUID'
    text
    {% for i in (1..3) %}
        {{- i }}
    {% endfor %}
    LIQUID
    );

    $output = iterator_to_array($stream);

    expect($output)->toBe([
        "text\n",
        '1',
        "\n",
        '2',
        "\n",
        '3',
        "\n",
    ]);
});

test('stream generator variable', function () {
    $stream = streamTemplate(<<<'LIQUID'
    {{ var }}
    LIQUID,
        staticData: [
            'var' => function () {
                yield 'text1';
                yield 'text2';
            },
        ]
    );

    $output = iterator_to_array($stream);

    expect($output)
        ->toHaveCount(2)
        ->{0}->toBe('text1')
        ->{1}->toBe('text2');
});

test('generator variable with filters is not streamed', function () {
    $stream = streamTemplate(<<<'LIQUID'
    {{ var | join: ',' }}
    LIQUID,
        staticData: [
            'var' => function () {
                yield 'text1';
                yield 'text2';
            },
        ]
    );

    $output = iterator_to_array($stream);

    expect($output)
        ->toHaveCount(1)
        ->{0}->toBe('text1,text2');
});

test('for tags stream their body chunks', function () {
    $stream = streamTemplate(
        '{% for item in items %}<item>{{ item }}</item>{% endfor %}',
        staticData: ['items' => ['a', 'bb']],
    );

    expect(iterator_to_array($stream))->toBe([
        '<item>',
        'a',
        '</item>',
        '<item>',
        'bb',
        '</item>',
    ]);
});

test('if, unless, and case tags stream their selected body chunks', function () {
    $stream = streamTemplate(
        '{% if enabled %}if:{{ value }}{% else %}no{% endif %}'
        .'{% unless disabled %}unless:{{ value }}{% else %}disabled{% endunless %}'
        .'{% case value %}{% when "x" %}case:{{ value }}{% else %}other{% endcase %}',
        staticData: [
            'enabled' => true,
            'disabled' => false,
            'value' => 'x',
        ],
    );

    expect(iterator_to_array($stream))->toBe([
        'if:',
        'x',
        'unless:',
        'x',
        'case:',
        'x',
    ]);
});

test('streamed for tags preserve break and continue behavior', function () {
    $environment = \Keepsuit\Liquid\Environment::default();
    $template = $environment->parseString(<<<'LIQUID'
    {% for item in items %}{{ item }}{% if item == 'b' %}{% continue %}{% endif %}x{% if item == 'c' %}{% break %}{% endif %}{% endfor %}
    LIQUID
    );

    $context = $environment->newRenderContext(staticData: [
        'items' => ['a', 'b', 'c', 'd'],
    ]);

    expect(implode('', iterator_to_array($template->stream($context))))
        ->toBe('axbcx');
});
