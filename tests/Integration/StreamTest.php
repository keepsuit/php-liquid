<?php

use Keepsuit\Liquid\Contracts\CanBeStreamed;
use Keepsuit\Liquid\Environment;
use Keepsuit\Liquid\EnvironmentFactory;
use Keepsuit\Liquid\Exceptions\ResourceLimitException;
use Keepsuit\Liquid\Parse\TagParseContext;
use Keepsuit\Liquid\Render\RenderContext;
use Keepsuit\Liquid\Render\ResourceLimits;
use Keepsuit\Liquid\Tag;
use Keepsuit\Liquid\Template;

class UnsupportedCompilerStreamTestTag extends Tag implements CanBeStreamed
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
        return 'runtime1runtime2';
    }

    public function stream(RenderContext $context): \Generator
    {
        yield 'runtime1';
        yield 'runtime2';
    }
}

function compileStreamTestTemplate(Environment $environment, Template $template): Template
{
    $path = tempnam(sys_get_temp_dir(), 'liquid-compiled-stream-');

    if ($path === false) {
        throw new RuntimeException('Unable to create a temporary compiled template path.');
    }

    unlink($path);
    $path .= '.php';

    try {
        $environment->compile($template, $path);

        /** @var Template $compiled */
        return require $path;
    } finally {
        @unlink($path);
    }
}

function streamChunks(Template $template, RenderContext $context): array
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

    // Node chunks are grouped, so a template this small arrives in one piece.
    expect($output)->toBe(["text\n1\n2\n3\n"]);
});

test('stream generator variable', function () {
    // Values sized to the grouping threshold, so consuming the generator one
    // value at a time stays visible: a materialised value could only arrive as
    // a single chunk.
    $stream = streamTemplate(<<<'LIQUID'
    {{ var }}
    LIQUID,
        staticData: [
            'var' => function () {
                yield str_repeat('a', 4096);
                yield str_repeat('b', 4096);
            },
        ]
    );

    $output = iterator_to_array($stream);

    expect($output)
        ->toHaveCount(2)
        ->{0}->toBe(str_repeat('a', 4096))
        ->{1}->toBe(str_repeat('b', 4096));
});

test('generator variable with filters is not streamed', function () {
    // The same values as above, but a filter has to materialise the generator
    // before it can run, so all of it is produced at once.
    $stream = streamTemplate(<<<'LIQUID'
    {{ var | join: ',' }}
    LIQUID,
        staticData: [
            'var' => function () {
                yield str_repeat('a', 4096);
                yield str_repeat('b', 4096);
            },
        ]
    );

    $output = iterator_to_array($stream);

    expect($output)
        ->toHaveCount(1)
        ->{0}->toBe(str_repeat('a', 4096).','.str_repeat('b', 4096));
});

test('for tags stream their body chunks', function () {
    $stream = streamTemplate(
        '{% for item in items %}<item>{{ item }}</item>{% endfor %}',
        staticData: ['items' => ['a', 'bb']],
    );

    expect(iterator_to_array($stream))->toBe(['<item>a</item><item>bb</item>']);
});

test('a loop larger than the grouping threshold keeps output flowing', function () {
    $stream = streamTemplate(
        '{% for item in items %}{{ item }}{% endfor %}',
        staticData: ['items' => array_fill(0, 8, str_repeat('x', 1024))],
    );

    // 8 KB cannot arrive as one chunk: output is emitted while the loop is
    // still running, which is what bounds memory on a large template.
    expect(iterator_to_array($stream))->toHaveCount(2);
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

    expect(iterator_to_array($stream))->toBe(['if:xunless:xcase:x']);
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

test('grouped output reaches the consumer before a rethrown error', function () {
    $environment = \Keepsuit\Liquid\EnvironmentFactory::new()->setRethrowErrors(true)->build();
    $template = $environment->parseString('HELLO WORLD {{ boom.standard_error }} tail');

    $context = $environment->newRenderContext(staticData: [
        'boom' => new \Keepsuit\Liquid\Tests\Stubs\ErrorDrop,
    ]);

    $received = [];
    try {
        foreach ($template->stream($context) as $chunk) {
            $received[] = $chunk;
        }
    } catch (\Throwable) {
        // the error is expected; what matters is what arrived before it
    }

    // Output already produced must not be discarded along with the exception.
    expect(implode('', $received))->toBe('HELLO WORLD ');
});

test('grouped output survives an error handler throwing a non liquid exception', function () {
    // A custom handler can throw anything, so what escapes the stream is not
    // always a LiquidException — the buffer still has to be flushed.
    $handler = new class implements \Keepsuit\Liquid\Contracts\LiquidErrorHandler
    {
        public function handle(\Throwable $error): string
        {
            throw new \RuntimeException('from handler');
        }
    };

    $environment = \Keepsuit\Liquid\EnvironmentFactory::new()
        ->setErrorHandler($handler)
        ->setRethrowErrors(false)
        ->build();
    $template = $environment->parseString('PREFIX {{ boom.standard_error }} tail');

    $context = $environment->newRenderContext(staticData: [
        'boom' => new \Keepsuit\Liquid\Tests\Stubs\ErrorDrop,
    ]);

    $received = [];
    expect(function () use ($template, $context, &$received) {
        foreach ($template->stream($context) as $chunk) {
            $received[] = $chunk;
        }
    })->toThrow(RuntimeException::class);

    expect(implode('', $received))->toBe('PREFIX ');
});

test('a consumer can stop reading a stream part way through', function () {
    $environment = \Keepsuit\Liquid\Environment::default();
    $template = $environment->parseString('{% for i in items %}{{ i }}{% endfor %}');

    $context = $environment->newRenderContext(staticData: [
        'items' => array_fill(0, 8, str_repeat('x', 1024)),
    ]);

    // Abandoning the generator force-closes it, so the pending buffer must not
    // be flushed from a finally block: yielding from one there is fatal.
    $first = null;
    foreach ($template->stream($context) as $chunk) {
        $first = $chunk;
        break;
    }

    expect($first)->toBe(str_repeat('x', 4096));
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

test('compiled stream preserves complete output', function () {
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

    expect(implode('', $optimized))
        ->toBe(implode('', $interpreted))
        ->toBe("text\ntext1text2");
});

test('compiled for loops stream each body chunk before the next iteration', function () {
    $environment = Environment::default();
    $template = $environment->parseString('{% for item in items %}{{ item }}{% endfor %}');
    $compiled = compileStreamTestTemplate($environment, $template);
    $context = $environment->newRenderContext(
        staticData: ['items' => ['a', 'bb', 'c']],
        resourceLimits: new ResourceLimits(renderLengthLimit: 1),
    );

    $stream = $compiled->stream($context);

    expect($stream->current())->toBe('a');
    expect(fn () => $stream->next())->toThrow(ResourceLimitException::class);
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

test('compiled stream preserves unsupported tag output', function () {
    $environment = EnvironmentFactory::new()
        ->registerTag(UnsupportedCompilerStreamTestTag::class)
        ->build();
    $template = $environment->parseString('before{% unsupported_compiler_stream %}after');
    $compiled = compileStreamTestTemplate($environment, $template);

    $interpreted = streamChunks($template, $environment->newRenderContext());
    $optimized = streamChunks($compiled, $environment->newRenderContext());

    expect($optimized)
        ->toBe($interpreted)
        ->toBe(['before', 'runtime1', 'runtime2', 'after']);
});

test('compiled stream preserves interrupts', function () {
    $environment = Environment::default();
    $template = $environment->parseString('before{% break %}after');
    $compiled = compileStreamTestTemplate($environment, $template);

    $interpreted = streamChunks($template, $environment->newRenderContext());
    $optimized = streamChunks($compiled, $environment->newRenderContext());

    expect(implode('', $optimized))
        ->toBe(implode('', $interpreted))
        ->toBe('before');
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
