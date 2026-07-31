<?php

use Keepsuit\Liquid\Environment;
use Keepsuit\Liquid\EnvironmentFactory;
use Keepsuit\Liquid\Exceptions\ResourceLimitException;
use Keepsuit\Liquid\Parse\TagParseContext;
use Keepsuit\Liquid\Render\RenderContext;
use Keepsuit\Liquid\Render\ResourceLimits;
use Keepsuit\Liquid\Tag;
use Keepsuit\Liquid\TemplateInterface;

class UnsupportedCompilerStreamTestTag extends Tag
{
    public static function tagName(): string
    {
        return 'unsupported_compiler_stream';
    }

    public function parse(TagParseContext $context): static
    {
        return $this;
    }

    public function render(RenderContext $context): string
    {
        return 'runtime';
    }
}

function compileStreamTestTemplate(Environment $environment, TemplateInterface $template): TemplateInterface
{
    $path = tempnam(sys_get_temp_dir(), 'liquid-compiled-stream-');

    if ($path === false) {
        throw new RuntimeException('Unable to create a temporary compiled template path.');
    }

    unlink($path);
    $path .= '.php';

    try {
        $environment->compile($template, $path);

        /** @var TemplateInterface $compiled */
        return require $path;
    } finally {
        @unlink($path);
    }
}

function streamChunks(TemplateInterface $template, RenderContext $context): array
{
    return iterator_to_array($template->stream($context));
}

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

test('compiled stream preserves lazy chunk boundaries', function () {
    $environment = Environment::default();
    $source = "text\n{{ var }}";
    $template = $environment->parseString($source, name: 'stream.liquid');
    $compiled = compileStreamTestTemplate($environment, $template);

    $interpreted = streamChunks($template, $environment->newRenderContext(staticData: [
        'var' => static function () {
            yield 'text1';
            yield 'text2';
        },
    ]));
    $optimized = streamChunks($compiled, $environment->newRenderContext(staticData: [
        'var' => static function () {
            yield 'text1';
            yield 'text2';
        },
    ]));

    expect($optimized)->toBe($interpreted)->toBe(["text\n", 'text1', 'text2']);
});

test('compiled stream does not evaluate until the generator is consumed', function () {
    $environment = Environment::default();
    $template = $environment->parseString('{{ value }}');
    $compiled = compileStreamTestTemplate($environment, $template);
    $evaluations = 0;
    $stream = $compiled->stream($environment->newRenderContext(staticData: [
        'value' => static function () use (&$evaluations): string {
            $evaluations++;

            return 'value';
        },
    ]));

    expect($stream)->toBeInstanceOf(Generator::class);
    expect($evaluations)->toBe(0);
    expect($stream->current())->toBe('value');
    expect($evaluations)->toBe(1);
});

test('compiled stream preserves filtered generator output as one chunk', function () {
    $environment = Environment::default();
    $template = $environment->parseString('{{ var | join: "," }}');
    $compiled = compileStreamTestTemplate($environment, $template);

    $factory = static fn (): \Generator => (static function () {
        yield 'text1';
        yield 'text2';
    })();

    $interpreted = streamChunks($template, $environment->newRenderContext(staticData: ['var' => $factory]));
    $optimized = streamChunks($compiled, $environment->newRenderContext(staticData: ['var' => $factory]));

    expect($optimized)->toBe($interpreted)->toBe(['text1,text2']);
});

test('compiled stream falls back to unsupported tag streaming behavior', function () {
    $environment = EnvironmentFactory::new()
        ->registerTag(UnsupportedCompilerStreamTestTag::class)
        ->build();
    $template = $environment->parseString('before{% unsupported_compiler_stream %}after');
    $compiled = compileStreamTestTemplate($environment, $template);

    $interpreted = streamChunks($template, $environment->newRenderContext());
    $optimized = streamChunks($compiled, $environment->newRenderContext());

    expect($optimized)->toBe($interpreted)->toBe(['before', 'runtime', 'after']);
});

test('compiled stream preserves interrupts and empty chunks', function () {
    $environment = Environment::default();
    $template = $environment->parseString('before{% break %}after');
    $compiled = compileStreamTestTemplate($environment, $template);

    $interpreted = streamChunks($template, $environment->newRenderContext());
    $optimized = streamChunks($compiled, $environment->newRenderContext());

    expect($optimized)->toBe($interpreted)->toBe(['before', '']);
});

test('compiled stream preserves resource-limit exceptions', function () {
    $environment = Environment::default();
    $template = $environment->parseString('0123456789', name: 'limited.liquid');
    $compiled = compileStreamTestTemplate($environment, $template);

    $interpretedContext = $environment->newRenderContext(
        resourceLimits: new ResourceLimits(renderLengthLimit: 9),
    );
    $compiledContext = $environment->newRenderContext(
        resourceLimits: new ResourceLimits(renderLengthLimit: 9),
    );

    expect(fn () => streamChunks($template, $interpretedContext))
        ->toThrow(ResourceLimitException::class);
    expect(fn () => streamChunks($compiled, $compiledContext))
        ->toThrow(ResourceLimitException::class);

    expect($compiledContext->resourceLimits->reached())
        ->toBe($interpretedContext->resourceLimits->reached())
        ->toBeTrue();
});
