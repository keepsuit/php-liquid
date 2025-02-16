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

    expect($output)
        ->toHaveCount(2)
        ->{0}->toBe("text\n")
        ->{1}->toBe("1\n2\n3\n");
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
