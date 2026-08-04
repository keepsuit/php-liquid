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

test('streaming enforces the render length limit across chunks', function () {
    $environment = \Keepsuit\Liquid\Environment::default();
    $template = $environment->parseString('{% for i in (1..6) %}{{ i }}{% endfor %}');

    $stream = $template->stream($environment->newRenderContext(
        resourceLimits: new \Keepsuit\Liquid\Render\ResourceLimits(renderLengthLimit: 5),
    ));

    expect(fn () => iterator_to_array($stream))
        ->toThrow(\Keepsuit\Liquid\Exceptions\ResourceLimitException::class);

    $stream = $template->stream($environment->newRenderContext(
        resourceLimits: new \Keepsuit\Liquid\Render\ResourceLimits(renderLengthLimit: 6),
    ));

    expect(implode('', iterator_to_array($stream)))->toBe('123456');
});

test('the render length limit covers chunks from partials and custom nodes', function () {
    $environment = \Keepsuit\Liquid\EnvironmentFactory::new()
        ->setFilesystem(new \Keepsuit\Liquid\Tests\Stubs\StubFileSystem(partials: ['snippet' => '{% streaming %}']))
        ->build();
    $environment->tagRegistry->register(\Keepsuit\Liquid\Tests\Stubs\StreamingTag::class);

    // The tag lives outside the library and yields its own chunks, nested one
    // partial deep: neither the tag nor the partial counts anything itself.
    $template = $environment->parseString('{% render "snippet" %}');

    $stream = $template->stream($environment->newRenderContext(
        resourceLimits: new \Keepsuit\Liquid\Render\ResourceLimits(renderLengthLimit: 5),
    ));

    expect(fn () => iterator_to_array($stream))
        ->toThrow(\Keepsuit\Liquid\Exceptions\ResourceLimitException::class);

    $stream = $template->stream($environment->newRenderContext(
        resourceLimits: new \Keepsuit\Liquid\Render\ResourceLimits(renderLengthLimit: 6),
    ));

    expect(implode('', iterator_to_array($stream)))->toBe('abcdef');
});

test('the render length limit caps one stream, not the context lifetime', function () {
    $environment = \Keepsuit\Liquid\Environment::default();
    $template = $environment->parseString('abcdefgh');
    $context = $environment->newRenderContext(
        resourceLimits: new \Keepsuit\Liquid\Render\ResourceLimits(renderLengthLimit: 10),
    );

    // Same scope the render path applies to a single render() call: streaming
    // twice through one context must not add up to the limit.
    expect(implode('', iterator_to_array($template->stream($context))))->toBe('abcdefgh');
    expect(implode('', iterator_to_array($template->stream($context))))->toBe('abcdefgh');
    expect($template->render($context))->toBe('abcdefgh');
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
