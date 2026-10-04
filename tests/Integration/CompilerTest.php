<?php

use Keepsuit\Liquid\Compiler\CodeBuilder;
use Keepsuit\Liquid\Compiler\CompiledTemplate;
use Keepsuit\Liquid\Compiler\CompilerContext;
use Keepsuit\Liquid\Compiler\UnsupportedNodeException;
use Keepsuit\Liquid\Condition\Condition;
use Keepsuit\Liquid\Contracts\CanBeCompiled;
use Keepsuit\Liquid\Contracts\CanBeExported;
use Keepsuit\Liquid\Contracts\CanBeStreamed;
use Keepsuit\Liquid\Contracts\Disableable;
use Keepsuit\Liquid\EnvironmentFactory;
use Keepsuit\Liquid\Exceptions\ResourceLimitException;
use Keepsuit\Liquid\Extensions\Extension;
use Keepsuit\Liquid\Filters\FiltersProvider;
use Keepsuit\Liquid\Nodes\BodyNode;
use Keepsuit\Liquid\Nodes\Document;
use Keepsuit\Liquid\Nodes\Node;
use Keepsuit\Liquid\Nodes\RangeLookup;
use Keepsuit\Liquid\Nodes\Raw;
use Keepsuit\Liquid\Nodes\Text;
use Keepsuit\Liquid\Nodes\Variable;
use Keepsuit\Liquid\Nodes\VariableLookup;
use Keepsuit\Liquid\Parse\TagParseContext;
use Keepsuit\Liquid\ParsedTemplate;
use Keepsuit\Liquid\Performance\Support\StorefrontTheme;
use Keepsuit\Liquid\Render\RenderContext;
use Keepsuit\Liquid\Render\ResourceLimits;
use Keepsuit\Liquid\Tag;
use Keepsuit\Liquid\Template;

class CompilableCompilerTestNode extends Node implements CanBeCompiled
{
    public function __construct(private readonly string $value) {}

    public function render(RenderContext $context): string
    {
        return $this->value;
    }

    public function compile(CompilerContext $context): void
    {
        $context->writeOutput($context->writeValue($this->value));
    }
}

class CompilableCompilerTestTag extends Tag implements CanBeCompiled
{
    public static function tagName(): string
    {
        return 'compiler_test';
    }

    public function parse(TagParseContext $context): static
    {
        return $this;
    }

    public function render(RenderContext $context): string
    {
        return 'tag output';
    }

    public function compile(CompilerContext $context): void
    {
        $context->writeOutput($context->writeValue('tag output'));
    }
}

class CompilerTestFilters extends FiltersProvider
{
    public function compilerMarker(string $value): string
    {
        return 'filtered '.$value;
    }

    public function compilerGenerator(mixed $value): Generator
    {
        yield $value;
        yield '!';
    }

    public function compilerLookup(mixed $value): VariableLookup
    {
        return new VariableLookup('target');
    }

    public function compilerEnumIdentity(mixed $value): string
    {
        return $value instanceof UnitEnum ? $value::class.'::'.$value->name : 'not an enum';
    }

    public function compilerArguments(mixed $value, mixed $first, mixed $second = null, mixed $named = null): string
    {
        return json_encode([$value, $first, $second, $named], JSON_THROW_ON_ERROR);
    }
}

enum CompilerUnitEnum
{
    case Ready;
}

enum CompilerBackedEnum: string
{
    case Ready = 'ready';
}

enum CompilerExportedEnum implements \Keepsuit\Liquid\Contracts\CanBeEvaluated, CanBeExported
{
    case Ready;

    public function evaluate(RenderContext $context): mixed
    {
        return 'custom enum';
    }

    public function export(CompilerContext $context): ?string
    {
        return $context->writeValue('custom enum');
    }
}

class CompilerCountingPartialValue implements \Keepsuit\Liquid\Contracts\CanBeEvaluated
{
    public int $calls = 0;

    public function evaluate(RenderContext $context): mixed
    {
        return ++$this->calls;
    }
}

class CompilerInheritedLookup extends VariableLookup
{
    public function evaluate(RenderContext $context): mixed
    {
        return [7, 8];
    }
}

class CompilerDynamicNameLookup extends VariableLookup
{
    public function evaluate(RenderContext $context): mixed
    {
        return 'snippet';
    }
}

class CompilerLiquidEventValue implements \Keepsuit\Liquid\Contracts\AsLiquidValue
{
    public function __construct(private ArrayObject $events, private string $name) {}

    public function toLiquidValue(): int
    {
        $this->events[] = $this->name.' value';

        return 1;
    }
}

trait CompilerSmallTagOverride
{
    public function render(RenderContext $context): string
    {
        return 'override';
    }

    public function stream(RenderContext $context): Generator
    {
        yield 'first';
        yield 'second';
    }
}

class CompilerCustomIfChanged extends \Keepsuit\Liquid\Tags\IfChanged implements CanBeStreamed, Disableable
{
    use CompilerSmallTagOverride;
}

class CompilerCustomTableRow extends \Keepsuit\Liquid\Tags\TableRowTag implements CanBeStreamed, Disableable
{
    use CompilerSmallTagOverride;
}

class CompilerCustomRenderedBody extends BodyNode
{
    public function render(RenderContext $context): string
    {
        return 'custom body';
    }
}

class CompilerInterruptValue implements \Keepsuit\Liquid\Contracts\CanBeEvaluated
{
    public function evaluate(RenderContext $context): string
    {
        $context->pushInterrupt(new \Keepsuit\Liquid\Interrupts\BreakInterrupt);

        return 'x';
    }
}

class CompilerCustomEchoTag extends \Keepsuit\Liquid\Tags\EchoTag implements CanBeStreamed, Disableable
{
    use CompilerSmallTagOverride;
}

class CompilerCustomIncrementTag extends \Keepsuit\Liquid\Tags\IncrementTag implements CanBeStreamed, Disableable
{
    use CompilerSmallTagOverride;
}

class CompilerCustomDecrementTag extends \Keepsuit\Liquid\Tags\DecrementTag implements CanBeStreamed, Disableable
{
    use CompilerSmallTagOverride;
}

class CompilerCustomCycleTag extends \Keepsuit\Liquid\Tags\CycleTag implements CanBeStreamed, Disableable
{
    use CompilerSmallTagOverride;
}

class CompilerCustomBreakTag extends \Keepsuit\Liquid\Tags\BreakTag implements CanBeStreamed, Disableable
{
    use CompilerSmallTagOverride;
}

class CompilerCustomContinueTag extends \Keepsuit\Liquid\Tags\ContinueTag implements CanBeStreamed, Disableable
{
    use CompilerSmallTagOverride;
}

class CompilerCustomRawTag extends \Keepsuit\Liquid\Tags\RawTag implements CanBeStreamed, Disableable
{
    use CompilerSmallTagOverride;
}

class CompilerCustomDocTag extends \Keepsuit\Liquid\Tags\DocTag implements CanBeStreamed, Disableable
{
    use CompilerSmallTagOverride;
}

class CompilerCustomRenderTag extends \Keepsuit\Liquid\Tags\RenderTag implements Disableable
{
    use CompilerSmallTagOverride;
}

class CompilerCustomDynamicRenderTag extends \Keepsuit\Liquid\Tags\Custom\DynamicRenderTag implements Disableable
{
    use CompilerSmallTagOverride;
}

class CompilerCustomRawBody extends Raw
{
    public function render(RenderContext $context): string
    {
        return 'body override';
    }
}

test('native raw and doc blocks avoid serialized bodies and preserve render scores', function (int $prefixLength) {
    $environment = EnvironmentFactory::new()->build();
    $prefix = str_repeat('p', $prefixLength);
    $raw = "\n{{ opaque | invalid }} \\\x00 \" \$";
    $template = $environment->parseString($prefix.'{% doc %}hidden documentation{% enddoc %}{% raw %}'.$raw.'{% endraw %}tail');
    $path = temporaryCompiledTemplatePath();

    try {
        $environment->compile($template, $path);
        $compiled = require $path;
        expect(file_get_contents($path))->not->toContain('unserialize(', 'hidden documentation');
        foreach ([$template, $compiled] as $candidate) {
            foreach (['render', 'stream'] as $mode) {
                $context = $environment->newRenderContext();
                $output = $mode === 'render' ? $candidate->render($context) : implode('', iterator_to_array($candidate->stream($context)));
                expect($output)->toBe($prefix.$raw.'tail');
                expect($context->resourceLimits->getRenderScore())->toBe(count($template->root->body->children()));
                $limited = $environment->newRenderContext(resourceLimits: new ResourceLimits(renderScoreLimit: 0));
                expect(fn () => $mode === 'render' ? $candidate->render($limited) : iterator_to_array($candidate->stream($limited)))
                    ->toThrow(ResourceLimitException::class);
            }
        }
    } finally {
        @unlink($path);
    }
})->with([0, 4096]);

test('raw and doc subclasses retain streamed overrides and disabled checks', function (string $class) {
    $environment = EnvironmentFactory::new()->registerTag($class)->build();
    $tag = $class::tagName();
    $template = $environment->parseString('before{% '.$tag.' %}ignored{% end'.$tag.' %}after');
    $path = temporaryCompiledTemplatePath();

    try {
        $environment->compile($template, $path);
        $compiled = require $path;
        foreach ([$template, $compiled] as $candidate) {
            expect($candidate->render($environment->newRenderContext()))->toBe('beforeoverrideafter');
            expect(iterator_to_array($candidate->stream($environment->newRenderContext()), false))
                ->toBe(['before', 'first', 'second', 'after']);
            $context = $environment->newRenderContext();
            expect($context->withDisabledTags([$tag], fn () => $candidate->render($context)))
                ->toBe('beforeLiquid error (line 1): '.$tag.' usage is not allowed in this contextafter');
        }
    } finally {
        @unlink($path);
    }
})->with([CompilerCustomRawTag::class, CompilerCustomDocTag::class]);

test('native raw tags retain custom body rendering', function () {
    $environment = EnvironmentFactory::new()->build();
    $template = $environment->parseString('before{% raw %}ignored{% endraw %}after');
    $tag = $template->root->body->children()[1];
    (new ReflectionProperty($tag, 'body'))->setValue($tag, new CompilerCustomRawBody('ignored'));
    $path = temporaryCompiledTemplatePath();

    try {
        $environment->compile($template, $path);
        $compiled = require $path;
        foreach ([$template, $compiled] as $candidate) {
            expect($candidate->render($environment->newRenderContext()))->toBe('beforebody overrideafter');
            expect(implode('', iterator_to_array($candidate->stream($environment->newRenderContext()))))->toBe('beforebody overrideafter');
        }
    } finally {
        @unlink($path);
    }
});

test('compiled unfiltered scalar literals preserve their output without runtime helpers', function (mixed $value, string $expected) {
    $environment = EnvironmentFactory::new()->build();
    $template = $environment->parseString('');
    $template->root->body->pushChild(new Variable($value));
    $path = temporaryCompiledTemplatePath();

    try {
        $environment->compile($template, $path);
        $compiled = require $path;
        expect(file_get_contents($path))->not->toContain('::streamValue', '::renderValue');
        foreach ([$template, $compiled] as $candidate) {
            expect($candidate->render($environment->newRenderContext()))->toBe($expected);
            expect(implode('', iterator_to_array($candidate->stream($environment->newRenderContext()))))->toBe($expected);
        }
    } finally {
        @unlink($path);
    }
})->with([
    [null, ''], [true, 'true'], [false, 'false'], [0, '0'], [-1, '-1'],
    [PHP_INT_MIN, (string) PHP_INT_MIN], [PHP_INT_MAX, (string) PHP_INT_MAX],
    ['', ''], ["\0\n\t\r\"\$\\", "\0\n\t\r\"\$\\"],
]);

test('small native tag compilers avoid serialized tag objects', function () {
    $environment = EnvironmentFactory::new()->setStrictVariables(true)->setStrictFilters(true)->setRethrowErrors(true)->build();
    $source = "{% echo value | upcase %}|{% increment counter %}|{% decrement counter %}|{% cycle 'a', 'b' %}|"
        .'{% for i in (1..3) %}{% if i == 1 %}{% continue %}{% endif %}{% echo i %}{% break %}{% endfor %}';
    $template = $environment->parseString($source);
    $path = temporaryCompiledTemplatePath();

    try {
        $environment->compile($template, $path);
        $compiled = require $path;
        expect(file_get_contents($path))->not->toContain('unserialize(');
        foreach ([$template, $compiled] as $candidate) {
            foreach ([false, true] as $stream) {
                $context = $environment->newRenderContext(data: ['value' => 'value']);
                expect($stream ? implode('', iterator_to_array($candidate->stream($context))) : $candidate->render($context))
                    ->toBe('VALUE|0|0|a|2');
                expect($context->getData('counter'))->toBe(0);
            }
        }
    } finally {
        @unlink($path);
    }
});

test('small native tag subclasses retain overrides stream boundaries and disabled checks', function (string $class, string $tag) {
    $environment = EnvironmentFactory::new()->registerTag($class)->build();
    $template = $environment->parseString('before{% '.$tag.' %}after');
    $path = temporaryCompiledTemplatePath();

    try {
        $environment->compile($template, $path);
        $compiled = require $path;
        foreach ([$template, $compiled] as $candidate) {
            expect($candidate->render($environment->newRenderContext()))->toBe('beforeoverrideafter');
            expect(iterator_to_array($candidate->stream($environment->newRenderContext()), false))
                ->toBe(['before', 'first', 'second', 'after']);
            foreach ([false, true] as $stream) {
                $context = $environment->newRenderContext();
                $output = $context->withDisabledTags([$class::tagName()], fn () => $stream
                    ? implode('', iterator_to_array($candidate->stream($context)))
                    : $candidate->render($context));
                expect($output)->toBe('beforeLiquid error (line 1): '.$class::tagName().' usage is not allowed in this contextafter');
                expect($context->getErrors())->toHaveCount(1);
            }
        }
    } finally {
        @unlink($path);
    }
})->with([
    [CompilerCustomEchoTag::class, "echo 'ignored'"],
    [CompilerCustomIncrementTag::class, 'increment counter'],
    [CompilerCustomDecrementTag::class, 'decrement counter'],
    [CompilerCustomCycleTag::class, "cycle 'a', 'b'"],
    [CompilerCustomBreakTag::class, 'break'],
    [CompilerCustomContinueTag::class, 'continue'],
]);

test('compiled echo consumes generator values atomically before yielding', function (bool $fail) {
    $environment = EnvironmentFactory::new()->build();
    $template = $environment->parseString('prefix{% echo values %}{% echo tail %}');
    $path = temporaryCompiledTemplatePath();

    try {
        $environment->compile($template, $path);
        $compiled = require $path;
        $outputs = [];
        foreach ([$template, $compiled] as $candidate) {
            $events = [];
            $values = (function () use (&$events, $fail) {
                $events[] = 'first';
                yield str_repeat('x', 4096);
                $events[] = 'second';
                if ($fail) {
                    throw new RuntimeException('echo failure');
                }
                yield 'y';
            })();
            $context = $environment->newRenderContext(data: [
                'values' => $values,
                'tail' => function () use (&$events) {
                    $events[] = 'tail';

                    return 'tail';
                },
            ]);
            $stream = $candidate->stream($context);
            $first = $stream->current();
            expect($events)->toBe(['first', 'second']);
            expect($first)->toBe($fail ? 'prefix' : 'prefix'.str_repeat('x', 4096).'y');
            $outputs[] = implode('', iterator_to_array($stream));
            expect($events)->toBe(['first', 'second', 'tail']);
            expect($context->getErrors())->toHaveCount($fail ? 1 : 0);
        }
        expect($outputs[0])->toBe($outputs[1]);
        if ($fail) {
            expect($outputs[0])->not->toContain('xxx');
        }
    } finally {
        @unlink($path);
    }
})->with([false, true]);

class CustomCompilerTestVariable extends Variable
{
    public function evaluate(RenderContext $context): mixed
    {
        return 'custom';
    }
}

class CompilerTestExtension extends Extension
{
    public function getTags(): array
    {
        return [CompilableCompilerTestTag::class, RuntimeFallbackCompilerTestTag::class];
    }

    public function getFiltersProviders(): array
    {
        return [CompilerTestFilters::class];
    }
}

class RuntimeFallbackCompilerTestTag extends Tag implements Disableable
{
    public static function tagName(): string
    {
        return 'runtime_fallback';
    }

    public function parse(TagParseContext $context): static
    {
        return $this;
    }

    public function render(RenderContext $context): string
    {
        return (string) $context->applyFilter('compiler_marker', 'runtime');
    }
}

class FailingCompilableCompilerTestNode extends Node implements CanBeCompiled
{
    public static int $compilations = 0;

    public function __construct(private readonly bool $unsupported = true) {}

    public function render(RenderContext $context): string
    {
        return 'fallback output';
    }

    public function compile(CompilerContext $context): void
    {
        self::$compilations++;
        $context->writeOutput($context->writeValue('partial output'));
        $context->writeRuntimeValue($this);
        $context->writeCachedValue(new VariableLookup('marker', ['value']));
        $context->writeRenderedBody(new BodyNode([new Text('discarded body')]), $context->temporaryVariable());

        throw $this->unsupported
            ? new UnsupportedNodeException('compiler test failure')
            : new RuntimeException('compiler test failure');
    }
}

class ReturningCompilableCompilerTestNode extends Node implements CanBeCompiled
{
    public static int $compilations = 0;

    public function render(RenderContext $context): string
    {
        return 'custom';
    }

    public function compile(CompilerContext $context): void
    {
        self::$compilations++;
        $context->writeOutput($context->writeValue('custom'));
        $context->write('return;');
    }
}

class RuntimeThrowingCompilableCompilerTestNode extends Node implements CanBeCompiled
{
    public function render(RenderContext $context): string
    {
        throw new RuntimeException('compiler test runtime failure');
    }

    public function compile(CompilerContext $context): void
    {
        $context->write(sprintf(
            'throw new \\RuntimeException(%s);',
            $context->writeValue('compiler test runtime failure'),
        ));
    }
}

class UnsafeFallbackCompilerTestNode extends Node
{
    public function __construct(private readonly mixed $value) {}

    public function render(RenderContext $context): string
    {
        return 'unsafe fallback';
    }
}

class RenderAndStreamCompilerTestNode extends Node implements CanBeStreamed
{
    public function render(RenderContext $context): string
    {
        return 'rendered';
    }

    public function stream(RenderContext $context): Generator
    {
        yield 'streamed';
    }
}

function temporaryCompiledTemplatePath(): string
{
    $path = tempnam(sys_get_temp_dir(), 'liquid-compiled-');

    if ($path === false) {
        throw new RuntimeException('Unable to create a temporary compiled template path.');
    }

    unlink($path);

    return $path.'.php';
}

test('compiled render and stream both surface the compiled body', function () {
    $compiled = new class extends CompiledTemplate
    {
        public function name(): ?string
        {
            return null;
        }

        protected function renderCompiled(RenderContext $context): iterable
        {
            yield 'compiled ';
            yield 'body';
        }

        protected function renderCompiledString(RenderContext $context): string
        {
            return 'compiled body';
        }
    };

    expect($compiled->render(new RenderContext))->toBe('compiled body');
    expect(iterator_to_array($compiled->stream(new RenderContext)))->toBe(['compiled ', 'body']);
});

test('compiled render uses render semantics for variables and fallback nodes', function () {
    $environment = EnvironmentFactory::new()->build();
    $template = $environment->parseString('{{ value }}|');
    assert($template instanceof ParsedTemplate);
    $template->root->body->pushChild(new RenderAndStreamCompilerTestNode);
    $path = temporaryCompiledTemplatePath();

    try {
        $environment->compile($template, $path);
        $compiled = require $path;
        $data = ['value' => new RenderAndStreamCompilerTestNode];

        expect($compiled->render($environment->newRenderContext(data: $data)))
            ->toBe($template->render($environment->newRenderContext(data: $data)))
            ->toBe('rendered|rendered');
        expect(implode('', iterator_to_array($compiled->stream($environment->newRenderContext(data: $data)))))
            ->toBe('streamed|streamed');
    } finally {
        @unlink($path);
    }
});

test('compiled direct lookups fully evaluate values returned from a scope', function () {
    $environment = EnvironmentFactory::new()->build();
    $template = $environment->parseString('{{ value }}|{{ value | upcase }}');
    $path = temporaryCompiledTemplatePath();

    try {
        $environment->compile($template, $path);
        $compiled = require $path;
        $data = ['value' => new VariableLookup('nested'), 'nested' => new VariableLookup('target'), 'target' => 'resolved'];

        expect($compiled->render($environment->newRenderContext(data: $data)))
            ->toBe($template->render($environment->newRenderContext(data: $data)))
            ->toBe('resolved|RESOLVED');
        expect(implode('', iterator_to_array($compiled->stream($environment->newRenderContext(data: $data)))))
            ->toBe('resolved|RESOLVED');
    } finally {
        @unlink($path);
    }
});

test('compiled filter chains preserve generator and evaluator results', function (string $source, string $expected) {
    $environment = EnvironmentFactory::new()->addExtension(new CompilerTestExtension)->build();
    $template = $environment->parseString($source);
    $path = temporaryCompiledTemplatePath();
    $data = ['value' => 'a', 'target' => 'resolved', 'suffix' => '?', 'allow_false' => true];

    try {
        $environment->compile($template, $path);
        $compiled = require $path;

        expect($compiled->render($environment->newRenderContext(data: $data)))
            ->toBe($template->render($environment->newRenderContext(data: $data)))
            ->toBe($expected);
        expect(implode('', iterator_to_array($compiled->stream($environment->newRenderContext(data: $data)))))
            ->toBe(implode('', iterator_to_array($template->stream($environment->newRenderContext(data: $data)))))
            ->toBe($expected);
    } finally {
        @unlink($path);
    }
})->with([
    'generator result' => ['{{ value | compiler_generator }}', 'a!'],
    'generator between filters' => ['{{ value | compiler_generator | join: "," }}', 'a,!'],
    'evaluator result' => ['{{ value | compiler_lookup }}', 'target'],
    'evaluator between filters' => ['{{ value | compiler_lookup | append: suffix }}', 'target?'],
    'named lookup argument' => ['{{ false | default: value, allow_false: allow_false }}', 'false'],
]);

test('compiled variables retain subclass evaluation', function (array $filters) {
    $environment = EnvironmentFactory::new()->build();
    $template = $environment->parseString('');
    assert($template instanceof ParsedTemplate);
    $template->root->body->setChildren([new CustomCompilerTestVariable(new VariableLookup('missing'), $filters)]);
    $path = temporaryCompiledTemplatePath();

    try {
        $environment->compile($template, $path);
        $compiled = require $path;

        expect($compiled->render($environment->newRenderContext()))
            ->toBe($template->render($environment->newRenderContext()))
            ->toBe('custom');
        expect(iterator_to_array($compiled->stream($environment->newRenderContext())))
            ->toBe(iterator_to_array($template->stream($environment->newRenderContext())))
            ->toBe(['custom']);
    } finally {
        @unlink($path);
    }
})->with(['unfiltered' => [[]], 'filtered' => [[['append', ['!'], []]]]]);

test('custom compiler fragments can return without skipping sibling nodes', function () {
    $environment = EnvironmentFactory::new()->build();
    $template = $environment->parseString('');
    assert($template instanceof ParsedTemplate);
    $template->root->body->setChildren([
        new Text('before'),
        new ReturningCompilableCompilerTestNode,
        new Text('after'),
    ]);
    $path = temporaryCompiledTemplatePath();
    ReturningCompilableCompilerTestNode::$compilations = 0;

    try {
        $environment->compile($template, $path);
        $compiled = require $path;

        expect(ReturningCompilableCompilerTestNode::$compilations)->toBe(1);

        expect($compiled->render($environment->newRenderContext()))
            ->toBe($template->render($environment->newRenderContext()))
            ->toBe('beforecustomafter');
        expect(implode('', iterator_to_array($compiled->stream($environment->newRenderContext()))))
            ->toBe('beforecustomafter');
    } finally {
        @unlink($path);
    }
});

test('compiled static partials preserve literal with expressions', function (string $expression) {
    $environment = EnvironmentFactory::new()
        ->setFilesystem(new \Keepsuit\Liquid\Tests\Stubs\StubFileSystem(['snippet' => '{{ snippet | default: "none" }}']))
        ->build();
    $template = $environment->parseString('{% render "snippet" with '.$expression.' %}');
    $path = temporaryCompiledTemplatePath();

    try {
        $environment->compile($template, $path);
        $compiled = require $path;

        expect($compiled->render($environment->newRenderContext()))
            ->toBe($template->render($environment->newRenderContext()));
        expect(implode('', iterator_to_array($compiled->stream($environment->newRenderContext()))))
            ->toBe(implode('', iterator_to_array($template->stream($environment->newRenderContext()))));
    } finally {
        @unlink($path);
    }
})->with(['0', 'false', '""', 'nil', '"value"']);

test('compiled templates buffer native chunks without using the render accumulator', function () {
    $environment = EnvironmentFactory::new()->build();
    $template = $environment->parseString('Hello {{ name }}!');
    $compiledPath = temporaryCompiledTemplatePath();

    try {
        $environment->compile($template, $compiledPath);
        $compiledSource = file_get_contents($compiledPath);

        expect($compiledSource)
            ->toContain('protected function renderCompiled(RenderContext $context): iterable')
            ->toContain('yield ')
            ->not->toContain('function () use ($context): iterable {')
            ->not->toContain('yieldBody')
            ->not->toContain('yield from [];')
            ->not->toContain('private function body')
            ->not->toContain('private function node')
            ->not->toContain('resourceLimits->incrementWriteScore')
            ->toContain('resourceLimits->incrementRenderScore')
            ->toContain('try {')
            ->toContain('catch (');

        $streamSource = strstr(
            strstr($compiledSource, 'protected function renderCompiled('),
            'protected function renderCompiledString(',
            before_needle: true,
        );
        expect($streamSource)->not->toContain('$output');
        expect($compiledSource)->not->toContain('new Variable(');

        /** @var CompiledTemplate $compiled */
        $compiled = require $compiledPath;
        $context = $environment->newRenderContext(data: ['name' => 'World']);

        expect(iterator_to_array($compiled->stream($context)))
            ->toBe(['Hello World!']);
        expect($compiled->render($environment->newRenderContext(data: ['name' => 'World'])))
            ->toBe('Hello World!');
    } finally {
        @unlink($compiledPath);
    }
});

test('compiled stream avoids redundant buffer guards', function (string $source) {
    $environment = EnvironmentFactory::new()
        ->setFilesystem(new \Keepsuit\Liquid\Tests\Stubs\StubFileSystem(['p' => '{{ value }}']))
        ->build();
    $template = $environment->parseString($source);
    $path = temporaryCompiledTemplatePath();

    try {
        $environment->compile($template, $path);
        $generated = file_get_contents($path);
        $guard = 'if \\(strlen\\(\\$buffer\\) >= 4096\\) \\{\\s*yield \\$buffer;\\s*\\$buffer = "";\\s*\\}';
        expect(preg_match('/'.$guard.'\\s*'.$guard.'/', $generated))->toBe(0);

        $compiled = require $path;
        foreach ([false, true] as $enabled) {
            foreach (['short', str_repeat('x', 4096)] as $value) {
                $data = ['value' => $value, 'enabled' => $enabled, 'items' => $enabled ? [1, 2] : []];
                expect(implode('', iterator_to_array($compiled->stream($environment->newRenderContext(data: $data)))))
                    ->toBe($template->render($environment->newRenderContext(data: $data)));
            }
        }
    } finally {
        @unlink($path);
    }
})->with([
    'adjacent native nodes' => 'a{{ value }}b{{ value }}c',
    'branch and loop joins' => '{% if enabled %}{{ value }}{% endif %}{% for i in items %}a{{ value }}{% endfor %}tail',
    'partial boundaries' => 'before{% render "p", value: value %}after',
]);

test('compiled body preserves buffered output when an exception interrupts a yield', function (string $source) {
    $environment = EnvironmentFactory::new()->setRethrowErrors(false)->build();
    $template = $environment->parseString($source);
    $path = temporaryCompiledTemplatePath();

    try {
        $environment->compile($template, $path);
        $compiled = require $path;
        $results = [];
        foreach ([$template, $compiled] as $candidate) {
            $context = $environment->newRenderContext(data: ['value' => str_repeat('x', 4096)]);
            // Template::stream forwards chunks through its own generator. Inject
            // into the body itself to exercise its buffer reset and catch path.
            $stream = $candidate instanceof ParsedTemplate
                ? $candidate->root->body->stream($context)
                : (new ReflectionMethod($candidate, 'renderCompiled'))->invoke($candidate, $context);
            $output = $stream->current();
            $output .= $stream->throw(new RuntimeException('consumer error'));
            $stream->next();
            while ($stream->valid()) {
                $output .= $stream->current();
                $stream->next();
            }
            $results[] = [$output, array_map(fn ($error) => $error->toLiquidErrorMessage(), $context->getErrors())];
        }
        expect($results[1])->toBe($results[0]);
    } finally {
        @unlink($path);
    }
})->with([
    '{{ value }}tail',
    '{{ "'.str_repeat('x', 4096).'" }}tail',
]);

test('compiled direct partial lookups still read updated context values', function () {
    $environment = EnvironmentFactory::new()
        ->setFilesystem(new \Keepsuit\Liquid\Tests\Stubs\StubFileSystem(['p' => '{{ value }}']))
        ->build();
    $template = $environment->parseString('{% render "p", value: product %}|{% assign product = "second" %}{% render "p", value: product %}');
    $path = temporaryCompiledTemplatePath();

    try {
        $environment->compile($template, $path);
        expect(file_get_contents($path))
            ->not->toContain("new VariableLookup('product', [])")
            ->toContain("'value' => ['product', []]");
        $compiled = require $path;
        foreach (['render', 'stream'] as $method) {
            $output = $compiled->$method($environment->newRenderContext(data: ['product' => 'first']));
            expect($method === 'stream' ? implode('', iterator_to_array($output)) : $output)->toBe('first|second');
        }
    } finally {
        @unlink($path);
    }
});

test('compiled lookup deduplication preserves typed paths and dynamic evaluators', function () {
    $context = new CompilerContext;
    $first = $context->writeCachedValue(new VariableLookup('value', ['field', 0]));
    expect($context->writeCachedValue(new VariableLookup('value', ['field', 0])))->toBe($first);
    expect($context->writeCachedValue(new VariableLookup('value', ['field', '0'])))->not->toBe($first);
    expect($context->writeCachedValue(new VariableLookup('other', ['field', 0])))->not->toBe($first);
    $dynamic = $context->writeCachedValue(new VariableLookup('value', [new VariableLookup('key')]));
    expect($context->writeCachedValue(new VariableLookup('value', [new VariableLookup('key')])))->not->toBe($dynamic);
});

test('failed compilation rolls back interned lookup properties', function () {
    $environment = EnvironmentFactory::new()
        ->setFilesystem(new \Keepsuit\Liquid\Tests\Stubs\StubFileSystem(['p' => '{{ value }}']))
        ->build();
    $template = $environment->parseString('prefix{% render "p", value: marker.value %}');
    assert($template instanceof ParsedTemplate);
    $children = $template->root->body->children();
    array_splice($children, 1, 0, [new FailingCompilableCompilerTestNode]);
    $template->root->body->setChildren($children);
    $path = temporaryCompiledTemplatePath();

    try {
        $environment->compile($template, $path);
        $compiled = require $path;
        expect($compiled->render($environment->newRenderContext(data: ['marker' => ['value' => 'tail']])))
            ->toBe('prefixfallback outputtail');
    } finally {
        @unlink($path);
    }
});

test('compiled liquid bodies match parsed output scopes errors and resource scores', function (string $body) {
    $environment = EnvironmentFactory::new()->setRethrowErrors(false)->build();
    $template = $environment->parseString("before{% liquid\n".$body."\n%}after{{ saved }}");
    $path = temporaryCompiledTemplatePath();

    try {
        $environment->compile($template, $path);
        expect(file_get_contents($path))->not->toContain('Keepsuit\\\\Liquid\\\\Tags\\\\LiquidTag');
        $compiled = require $path;
        foreach (['render', 'stream'] as $method) {
            $results = [];
            foreach ([$template, $compiled] as $candidate) {
                $context = $environment->newRenderContext(data: ['boom' => new \Keepsuit\Liquid\Tests\Stubs\ErrorDrop]);
                $output = $candidate->$method($context);
                $results[] = [
                    $method === 'stream' ? implode('', iterator_to_array($output)) : $output,
                    $context->get('saved'),
                    $context->resourceLimits->getRenderScore(),
                    $context->resourceLimits->getCumulativeRenderScore(),
                    $context->resourceLimits->getAssignScore(),
                    $context->resourceLimits->getCumulativeAssignScore(),
                    array_map(fn ($error) => $error->toLiquidErrorMessage(), $context->getErrors()),
                ];
            }
            expect($results[1])->toBe($results[0]);
        }
    } finally {
        @unlink($path);
    }
})->with([
    'assignments' => "assign saved = 'one'\necho saved\nassign saved = 'two'\necho saved",
    'nested capture' => "capture saved\necho 'a'\ncapture inner\necho 'b'\nendcapture\necho inner\nendcapture\necho saved",
    'loop interrupts' => "for i in (1..4)\nif i == 2\ncontinue\nendif\necho i\nif i == 3\nbreak\nendif\nendfor",
    'handled errors' => "echo 'prefix'\necho boom.standard_error\necho 'tail'",
]);

test('compiled liquid streaming evaluates the complete body before yielding', function () {
    $environment = EnvironmentFactory::new()->build();
    $template = $environment->parseString("{% liquid\necho first\necho second\n%}");
    $path = temporaryCompiledTemplatePath();

    try {
        $environment->compile($template, $path);
        $compiled = require $path;
        foreach ([$template, $compiled] as $candidate) {
            $calls = 0;
            $context = $environment->newRenderContext(data: [
                'first' => str_repeat('x', 4096),
                'second' => function () use (&$calls) {
                    $calls++;

                    return 'tail';
                },
            ]);
            $stream = $candidate->stream($context);
            expect($calls)->toBe(0);
            expect($stream->current())->toBe(str_repeat('x', 4096).'tail');
            expect($calls)->toBe(1);
            unset($stream);
        }
    } finally {
        @unlink($path);
    }
});

test('compiled empty bodies return an empty iterable without generator noise', function () {
    $environment = EnvironmentFactory::new()->build();
    $compiledPath = temporaryCompiledTemplatePath();

    try {
        $environment->compile($environment->parseString(''), $compiledPath);
        $compiledSource = file_get_contents($compiledPath);

        /** @var CompiledTemplate $compiled */
        $compiled = require $compiledPath;

        expect($compiledSource)
            ->toContain('return [];')
            ->not->toContain('yield from [];');
        expect($compiled->render($environment->newRenderContext()))->toBe('');
        expect(iterator_to_array($compiled->stream($environment->newRenderContext())))->toBe([]);
    } finally {
        @unlink($compiledPath);
    }
});

test('environment compiles a template to a requireable artifact', function () {
    $environment = EnvironmentFactory::new()->build();
    $template = $environment->parseString('Hello {{ name }}');
    $compiledPath = temporaryCompiledTemplatePath();

    try {
        $environment->compile($template, $compiledPath);

        expect($compiledPath)->toBeFile();

        /** @var CompiledTemplate $compiled */
        $compiled = require $compiledPath;

        expect($compiled)->toBeInstanceOf(CompiledTemplate::class);
        expect($compiled->render($environment->newRenderContext(data: ['name' => 'World'])))
            ->toBe('Hello World');
    } finally {
        @unlink($compiledPath);
    }
});

test('compilation does not change interpreted template rendering', function () {
    $environment = EnvironmentFactory::new()->build();
    $template = $environment->parseString('Hello {{ name }}');
    $compiledPath = temporaryCompiledTemplatePath();

    try {
        $environment->compile($template, $compiledPath);

        expect($template->render($environment->newRenderContext(data: ['name' => 'World'])))
            ->toBe('Hello World');
    } finally {
        @unlink($compiledPath);
    }
});

test('compiled control flow preserves branch selection and stream output', function () {
    $environment = EnvironmentFactory::new()->build();
    $template = $environment->parseString(
        '{% if enabled %}if{% elsif other %}elsif{% else %}else{% endif %}|'
        .'{% unless disabled %}unless{% else %}not{% endunless %}|'
        .'{% case value %}{% when "a" %}A{% when "b" %}B{% else %}C{% endcase %}',
    );
    $compiledPath = temporaryCompiledTemplatePath();

    try {
        $environment->compile($template, $compiledPath);

        $compiledSource = file_get_contents($compiledPath);

        expect($compiledSource)->toContain('->conditionTruthy(')->toContain('::compare(');

        /** @var CompiledTemplate $compiled */
        $compiled = require $compiledPath;
        $data = ['enabled' => false, 'other' => true, 'disabled' => true, 'value' => 'b'];

        expect($compiled->render($environment->newRenderContext(data: $data)))
            ->toBe($template->render($environment->newRenderContext(data: $data)))
            ->toBe('elsif|not|B');

        $streamed = iterator_to_array(
            $compiled->stream($environment->newRenderContext(data: $data)),
        );

        expect(implode('', $streamed))->toBe('elsif|not|B');
    } finally {
        @unlink($compiledPath);
    }
});

test('compiled conditions preserve else behavior', function () {
    $environment = EnvironmentFactory::new()->build();
    $cases = [
        ['{% if false %}a{% else %}b{% elsif true %}c{% endif %}', [], 'b'],
        ['{% case value %}{% else %}b{% endcase %}', ['value' => 'a'], 'b'],
    ];

    foreach ($cases as [$source, $data, $expected]) {
        $template = $environment->parseString($source);
        $compiledPath = temporaryCompiledTemplatePath();

        try {
            $environment->compile($template, $compiledPath);

            /** @var CompiledTemplate $compiled */
            $compiled = require $compiledPath;
            $context = $environment->newRenderContext(data: $data);

            expect($compiled->render($context))
                ->toBe($template->render($environment->newRenderContext(data: $data)))
                ->toBe($expected);
        } finally {
            @unlink($compiledPath);
        }
    }
});

test('compiled dynamic partials preserve lookups loops isolation and errors', function (string $source, array $data, bool $strict) {
    $environment = EnvironmentFactory::new()
        ->registerTag(\Keepsuit\Liquid\Tags\Custom\DynamicRenderTag::class)
        ->setStrictVariables($strict)
        ->setFilesystem(new \Keepsuit\Liquid\Tests\Stubs\StubFileSystem([
            'snippet' => '{{ item }}:{{ suffix }}:{{ forloop.index }}:{{ outer }};',
        ]))->build();
    $template = $environment->parseString($source, name: 'dynamic.liquid');
    $compiledPath = temporaryCompiledTemplatePath();

    try {
        $environment->compile($template, $compiledPath);
        $compiled = require $compiledPath;
        expect(file_get_contents($compiledPath))->not->toContain('unserialize(');
        foreach (['render', 'stream'] as $mode) {
            $outputs = [];
            $errors = [];
            $scores = [];
            foreach ([$template, $compiled] as $candidate) {
                $context = $environment->newRenderContext(data: $data);
                $outputs[] = $mode === 'render' ? $candidate->render($context)
                    : implode('', iterator_to_array($candidate->stream($context), false));
                $errors[] = array_map(fn ($error) => [$error::class, $error->getMessage(), $error->lineNumber, $error->templateName], $candidate->getErrors());
                $scores[] = $context->resourceLimits->getRenderScore();
                expect(invade($context)->scopes)->toHaveCount(1);
            }
            expect($outputs[1])->toBe($outputs[0]);
            expect($errors[1])->toBe($errors[0]);
            expect($scores[1])->toBe($scores[0]);
        }
    } finally {
        @unlink($compiledPath);
    }
})->with([
    ['{% render name with value as item, suffix: suffix %}', ['name' => 'snippet', 'value' => 'a', 'suffix' => 's', 'outer' => 'isolated'], false],
    ['{% render names[key] for items as item, suffix: suffix %}', ['names' => ['p' => 'snippet'], 'key' => 'p', 'items' => [1, 2, 3], 'suffix' => 's'], false],
    ['{% render "snippet" with value as item %}', ['value' => 'a'], false],
    ['{% render name %}tail', ['name' => false], false],
    ['{% render name %}tail', [], false],
    ['{% render name %}tail', [], true],
]);

test('dynamic partial names do not resolve evaluators returned by the lookup', function () {
    $environment = EnvironmentFactory::new()->registerTag(\Keepsuit\Liquid\Tags\Custom\DynamicRenderTag::class)->build();
    $template = $environment->parseString('{% render name %}tail');
    $path = temporaryCompiledTemplatePath();

    try {
        $environment->compile($template, $path);
        $compiled = require $path;
        foreach ([$template, $compiled] as $candidate) {
            foreach (['render', 'stream'] as $mode) {
                $value = new CompilerCountingPartialValue;
                $context = $environment->newRenderContext(data: ['name' => $value]);
                $output = $mode === 'render' ? $candidate->render($context)
                    : implode('', iterator_to_array($candidate->stream($context), false));
                expect($output)->toBe('Liquid syntax error (line 1): Template name must be a stringtail');
                expect($value->calls)->toBe(0);
            }
        }
    } finally {
        @unlink($path);
    }
});

test('dynamic partial names preserve lookup subclass evaluation', function () {
    $environment = EnvironmentFactory::new()->registerTag(\Keepsuit\Liquid\Tags\Custom\DynamicRenderTag::class)
        ->setFilesystem(new \Keepsuit\Liquid\Tests\Stubs\StubFileSystem(['snippet' => 'custom lookup']))->build();
    $template = $environment->parseString('{% render ignored %}');
    assert($template instanceof ParsedTemplate);
    invade($template->root->body->children()[0])->templateNameExpression = new CompilerDynamicNameLookup('ignored');
    $path = temporaryCompiledTemplatePath();

    try {
        $environment->compile($template, $path);
        $compiled = require $path;
        foreach ([$template, $compiled] as $candidate) {
            expect($candidate->render($environment->newRenderContext()))->toBe('custom lookup');
            expect(implode('', iterator_to_array($candidate->stream($environment->newRenderContext()), false)))->toBe('custom lookup');
        }
    } finally {
        @unlink($path);
    }
});

test('compiled static render tags stream partials without rebuilding the tag', function () {
    $environment = EnvironmentFactory::new()
        ->setFilesystem(new \Keepsuit\Liquid\Tests\Stubs\StubFileSystem([
            'snippet' => 'partial {{ value }}',
        ]))
        ->build();
    $template = $environment->parseString('before {% render "snippet", value: value %} after');
    $compiledPath = temporaryCompiledTemplatePath();

    try {
        $environment->compile($template, $compiledPath);

        $compiledSource = file_get_contents($compiledPath);

        expect($compiledSource)
            ->toContain('yieldPartial', 'renderPartial')
            ->not->toContain('new VariableLookup(')
            ->not->toContain('deepclone_from_array')
            ->not->toContain('yield from [];')
            // The partial is looked up when the compiled template runs, never
            // inlined into the artifact.
            ->not->toContain('partial ');

        /** @var CompiledTemplate $compiled */
        $compiled = require $compiledPath;
        $data = ['value' => 'hello'];

        expect($compiled->render($environment->newRenderContext(data: $data)))
            ->toBe($template->render($environment->newRenderContext(data: $data)))
            ->toBe('before partial hello after');
        expect(implode('', iterator_to_array(
            $compiled->stream($environment->newRenderContext(data: $data)),
        )))->toBe('before partial hello after');
    } finally {
        @unlink($compiledPath);
    }
});

test('compiled static render loops avoid serialized tags and preserve loop behavior', function () {
    $environment = EnvironmentFactory::new()
        ->setFilesystem(new \Keepsuit\Liquid\Tests\Stubs\StubFileSystem([
            'product' => '{{ product.title }} ',
        ]))
        ->build();
    $template = $environment->parseString('{% render "product" for products %}');
    $compiledPath = temporaryCompiledTemplatePath();

    try {
        $environment->compile($template, $compiledPath);

        $compiledSource = file_get_contents($compiledPath);

        expect($compiledSource)
            ->toContain('yieldPartialLoop', 'renderPartialLoop')
            ->not->toContain('new VariableLookup(')
            ->not->toContain('unserialize(');

        /** @var CompiledTemplate $compiled */
        $compiled = require $compiledPath;
        $data = ['products' => [['title' => 'one'], ['title' => 'two']]];

        expect($compiled->render($environment->newRenderContext(data: $data)))
            ->toBe($template->render($environment->newRenderContext(data: $data)))
            ->toBe('one two ');
        expect(implode('', iterator_to_array($compiled->stream($environment->newRenderContext(data: $data)))))
            ->toBe('one two ');
    } finally {
        @unlink($compiledPath);
    }
});

test('compiled render loops preserve collections per-iteration attributes and isolation', function (string $kind) {
    $environment = EnvironmentFactory::new()
        ->setFilesystem(new \Keepsuit\Liquid\Tests\Stubs\StubFileSystem([
            'partials/item' => '{{ item }}:{{ note }}:{{ forloop.index | default: "once" }}:{{ outer | default: "isolated" }};{% assign outer = "changed" %}',
        ]))->build();
    $template = $environment->parseString('{% assign outer = "root" %}{% render "partials/item" for items, note: note %}{{ outer }}');
    $path = temporaryCompiledTemplatePath();

    try {
        $environment->compile($template, $path);
        $compiled = require $path;
        foreach (['render', 'stream'] as $mode) {
            $results = [];
            foreach ([$template, $compiled] as $candidate) {
                $items = match ($kind) {
                    'empty' => [],
                    'array' => [1, 2, 3],
                    'hash' => ['a' => 1, 'b' => 2, 'c' => 3],
                    'range' => new \Keepsuit\Liquid\Nodes\Range(1, 3),
                    'iterator' => new ArrayIterator([1, 2, 3]),
                    'generator' => (function () {
                        yield 1;
                        yield 2;
                        yield 3;
                    })(),
                    'scalar' => 'value',
                    'nil' => null,
                };
                $note = new CompilerCountingPartialValue;
                $context = $environment->newRenderContext(data: ['items' => $items, 'note' => $note]);
                $output = $mode === 'render' ? $candidate->render($context) : implode('', iterator_to_array($candidate->stream($context)));
                $results[] = [$output, $note->calls, $context->get('outer'), $context->resourceLimits->getRenderScore()];
            }
            expect($results[1])->toBe($results[0]);
            expect($results[1][1])->toBe($kind === 'empty' ? 0 : (in_array($kind, ['scalar', 'nil'], true) ? 1 : 3));
            expect($results[1][2])->toBe('root');
        }
    } finally {
        @unlink($path);
    }
})->with(['empty', 'array', 'hash', 'range', 'iterator', 'generator', 'scalar', 'nil']);

test('compiled render loops preserve lookup subclasses with inherited exporters', function (string $field, string $expected) {
    $environment = EnvironmentFactory::new()
        ->setFilesystem(new \Keepsuit\Liquid\Tests\Stubs\StubFileSystem(['p' => '{{ i }}:{{ note }};']))->build();
    $template = $environment->parseString('{% render "p" for items as i, note: note %}');
    $tag = $template->root->body->children()[0];
    $value = $field === 'attributes' ? ['note' => new CompilerInheritedLookup('note')] : new CompilerInheritedLookup('items');
    (new ReflectionProperty($tag, $field))->setValue($tag, $value);
    $path = temporaryCompiledTemplatePath();

    try {
        $environment->compile($template, $path);
        $compiled = require $path;
        foreach ([$template, $compiled] as $candidate) {
            $data = ['items' => [1, 2], 'note' => 'default'];
            expect($candidate->render($environment->newRenderContext(data: $data)))->toBe($expected);
            expect(implode('', iterator_to_array($candidate->stream($environment->newRenderContext(data: $data)))))->toBe($expected);
        }
    } finally {
        @unlink($path);
    }
})->with([
    ['variableNameExpression', '7:default;8:default;'],
    ['attributes', '1:78;2:78;'],
]);

test('compiled render loops materialize generators before streaming partials', function () {
    $environment = EnvironmentFactory::new()
        ->setFilesystem(new \Keepsuit\Liquid\Tests\Stubs\StubFileSystem(['p' => '{{ item }}{{ forloop.index }}']))
        ->build();
    $template = $environment->parseString('{% render "p" for items as item %}');
    $path = temporaryCompiledTemplatePath();

    try {
        $environment->compile($template, $path);
        $compiled = require $path;
        foreach ([$template, $compiled] as $candidate) {
            $events = [];
            $items = (function () use (&$events) {
                $events[] = 1;
                yield 1;
                $events[] = 2;
                yield 2;
            })();
            $context = $environment->newRenderContext(data: ['items' => $items]);
            $stream = $candidate->stream($context);
            expect($stream->current())->toStartWith('11');
            expect($events)->toBe([1, 2]);
            expect(implode('', iterator_to_array($stream)))->toBe('1122');
        }
    } finally {
        @unlink($path);
    }
});

test('compiled render loops preserve alias and attribute override order', function (string $arguments, string $body) {
    $environment = EnvironmentFactory::new()
        ->setFilesystem(new \Keepsuit\Liquid\Tests\Stubs\StubFileSystem(['p' => $body]))->build();
    $template = $environment->parseString('{% render "p" for items '.$arguments.' %}');
    $path = temporaryCompiledTemplatePath();

    try {
        $environment->compile($template, $path);
        $compiled = require $path;
        foreach (['render', 'stream'] as $mode) {
            $results = [];
            foreach ([$template, $compiled] as $candidate) {
                $context = $environment->newRenderContext(data: ['items' => [1, 2]]);
                $results[] = $mode === 'render' ? $candidate->render($context) : implode('', iterator_to_array($candidate->stream($context)));
            }
            expect($results[1])->toBe($results[0]);
        }
    } finally {
        @unlink($path);
    }
})->with([
    ['as forloop', '{{ forloop }}'],
    ['as item, item: "override", forloop: "overridden"', '{{ item }}{{ forloop }}'],
]);

test('render tag subclasses retain render and stream overrides when compiled', function (string $modifier, string $class) {
    $environment = EnvironmentFactory::new()->registerTag($class)
        ->setFilesystem(new \Keepsuit\Liquid\Tests\Stubs\StubFileSystem(['p' => 'ignored']))->build();
    $template = $environment->parseString('before{% render "p" '.$modifier.' items %}after');
    $path = temporaryCompiledTemplatePath();

    try {
        $environment->compile($template, $path);
        $compiled = require $path;
        foreach ([$template, $compiled] as $candidate) {
            expect($candidate->render($environment->newRenderContext()))->toBe('beforeoverrideafter');
            expect(implode('', iterator_to_array($candidate->stream($environment->newRenderContext()), false)))->toBe('beforefirstsecondafter');
            foreach (['render', 'stream'] as $mode) {
                $context = $environment->newRenderContext();
                $output = $context->withDisabledTags(['render'], fn () => $mode === 'render'
                    ? $candidate->render($context) : implode('', iterator_to_array($candidate->stream($context))));
                expect($output)->toBe('beforeLiquid error (line 1): render usage is not allowed in this contextafter');
                expect($context->getErrors())->toHaveCount(1);
            }
        }
    } finally {
        @unlink($path);
    }
})->with([
    ['with', CompilerCustomRenderTag::class],
    ['for', CompilerCustomRenderTag::class],
    ['with', CompilerCustomDynamicRenderTag::class],
    ['for', CompilerCustomDynamicRenderTag::class],
]);

test('compiled conditional bodies preserve interrupts from fallback nodes', function () {
    $environment = EnvironmentFactory::new()->build();
    $template = $environment->parseString('{% if stop %}{% break %}{% endif %}after');
    $compiledPath = temporaryCompiledTemplatePath();

    try {
        $environment->compile($template, $compiledPath);

        /** @var CompiledTemplate $compiled */
        $compiled = require $compiledPath;

        expect($compiled->render($environment->newRenderContext(data: ['stop' => true])))
            ->toBe($template->render($environment->newRenderContext(data: ['stop' => true])))
            ->toBe('');
        expect($compiled->render($environment->newRenderContext(data: ['stop' => false])))
            ->toBe('after');
    } finally {
        @unlink($compiledPath);
    }
});

test('compiled nested bodies stop at an interrupt exactly where the parsed template does', function (string $source, array $data) {
    $environment = EnvironmentFactory::new()->build();
    $template = $environment->parseString($source);
    $compiledPath = temporaryCompiledTemplatePath();

    try {
        $environment->compile($template, $compiledPath);

        /** @var CompiledTemplate $compiled */
        $compiled = require $compiledPath;

        expect($compiled->render($environment->newRenderContext(data: $data)))
            ->toBe($template->render($environment->newRenderContext(data: $data)));
    } finally {
        @unlink($compiledPath);
    }
})->with([
    // A break inside a for body must end that iteration and the loop, while
    // leaving the text after the loop in the outer body intact.
    'break inside a loop' => ['a{% for i in (1..5) %}<{{ i }}{% if i > 2 %}{% break %}{% endif %}>{% endfor %}b', []],
    'continue inside a loop' => ['a{% for i in (1..5) %}<{{ i }}{% if i == 2 %}{% continue %}{% endif %}>{% endfor %}b', []],
    // Text siblings after the interrupt must be skipped at every nesting level.
    'interrupt with trailing siblings' => ['a{% if stop %}x{% break %}y{% endif %}z', ['stop' => true]],
    'interrupt not taken' => ['a{% if stop %}x{% break %}y{% endif %}z', ['stop' => false]],
    'nested loops' => ['{% for i in (1..3) %}{% for j in (1..3) %}{{ i }}{{ j }}{% if j == 2 %}{% break %}{% endif %}{% endfor %}|{% endfor %}', []],
]);

test('compiled for bodies run in the caller frame without callback generators', function () {
    $environment = EnvironmentFactory::new()->build();
    $template = $environment->parseString('{% for i in items %}{{ i }}{% else %}none{% endfor %}');
    $compiledPath = temporaryCompiledTemplatePath();

    try {
        $environment->compile($template, $compiledPath);

        expect(file_get_contents($compiledPath))
            ->not->toContain('function (RenderContext $context): iterable {')
            ->not->toContain('->streamBlocks($context')
            ->toContain('::collectionSegmentForLookups($context,')
            ->toContain('foreach (')
            ->toContain('::leaveLoop($context);')
            ->not->toContain('collectCompiled')
            ->not->toContain('yieldBody')
            ->not->toContain('yield from [];')
            ->not->toContain('private function body')
            ->not->toContain('private function node');

        /** @var CompiledTemplate $compiled */
        $compiled = require $compiledPath;

        foreach ([['items' => ['a', 'b', 'c']], ['items' => []]] as $data) {
            expect($compiled->render($environment->newRenderContext(data: $data)))
                ->toBe($template->render($environment->newRenderContext(data: $data)));
        }
    } finally {
        @unlink($compiledPath);
    }
});

test('empty compiled for bodies do not allocate callback generators', function () {
    $environment = EnvironmentFactory::new()->build();
    $template = $environment->parseString('{% for i in items %}{% else %}{% endfor %}');
    $compiledPath = temporaryCompiledTemplatePath();

    try {
        $environment->compile($template, $compiledPath);

        $compiledSource = file_get_contents($compiledPath);

        expect($compiledSource)
            ->not->toContain('function (RenderContext $context): iterable {')
            ->not->toContain('yield from [];');
        expect($compiledSource)->not->toContain('return [];');

        /** @var CompiledTemplate $compiled */
        $compiled = require $compiledPath;

        foreach ([['items' => ['a']], ['items' => []]] as $data) {
            expect($compiled->render($environment->newRenderContext(data: $data)))
                ->toBe($template->render($environment->newRenderContext(data: $data)))
                ->toBe('');
        }
    } finally {
        @unlink($compiledPath);
    }
});

test('compiled for loops match parsed rendering and streaming scopes and scores', function (string $source, array $data) {
    $environment = EnvironmentFactory::new()->build();
    $template = $environment->parseString($source);
    $compiledPath = temporaryCompiledTemplatePath();

    try {
        $environment->compile($template, $compiledPath);

        /** @var CompiledTemplate $compiled */
        $compiled = require $compiledPath;

        foreach (['render', 'stream'] as $method) {
            $parsedContext = $environment->newRenderContext(data: $data + ['i' => 'outer']);
            $compiledContext = $environment->newRenderContext(data: $data + ['i' => 'outer']);
            $parsedOutput = $template->$method($parsedContext);
            $compiledOutput = $compiled->$method($compiledContext);
            if ($method === 'stream') {
                $parsedOutput = implode('', iterator_to_array($parsedOutput));
                $compiledOutput = implode('', iterator_to_array($compiledOutput));
            }
            expect($compiledOutput)->toBe($parsedOutput);
            expect($compiledContext->getRegister('for'))->toBe($parsedContext->getRegister('for'));
            expect($compiledContext->getRegister('for_stack'))->toBe($parsedContext->getRegister('for_stack'));
            expect($compiledContext->get('i'))->toBe('outer');
            expect($compiledContext->resourceLimits->getRenderScore())->toBe($parsedContext->resourceLimits->getRenderScore());
        }
    } finally {
        @unlink($compiledPath);
    }
})->with([
    'forloop drop' => ['{% for i in items %}{{ forloop.index }}/{{ forloop.length }}{% if forloop.first %}F{% endif %}{% if forloop.last %}L{% endif %} {% endfor %}', ['items' => ['a', 'b', 'c']]],
    'nested loops share the parent drop' => ['{% for i in outer %}{% for j in inner %}{{ forloop.parentloop.index }}.{{ forloop.index }} {% endfor %}{% endfor %}', ['outer' => [1, 2], 'inner' => [1, 2]]],
    'limit and offset' => ['{% for i in items limit: 2 offset: 1 %}{{ i }}{% endfor %}', ['items' => [1, 2, 3, 4, 5]]],
    'offset continue' => ['{% for i in items limit: 2 %}{{ i }}{% endfor %}|{% for i in items offset: continue %}{{ i }}{% endfor %}', ['items' => [1, 2, 3, 4, 5]]],
    'dynamic limit and offset' => ['{% for i in items limit: amount offset: start %}{{ i }}{% endfor %}', ['items' => [1, 2, 3, 4, 5], 'amount' => 2, 'start' => 1]],
    'reversed' => ['{% for i in items reversed %}{{ i }}{% endfor %}', ['items' => [1, 2, 3]]],
    'range' => ['{% for i in (1..4) %}{{ i }}{% endfor %}', []],
    'else branch' => ['{% for i in items %}{{ i }}{% else %}empty{% endfor %}', ['items' => []]],
    'break out of nested loop' => ['{% for i in outer %}{% for j in inner %}{{ j }}{% break %}{% endfor %}|{% endfor %}', ['outer' => [1, 2], 'inner' => [1, 2, 3]]],
    'continue skips' => ['{% for i in items %}{% if i == 2 %}{% continue %}{% endif %}{{ i }}{% endfor %}', ['items' => [1, 2, 3]]],
]);

test('native compiled assignments and captures preserve values scopes and resource scores', function (string $source, array $data) {
    $environment = EnvironmentFactory::new()->addExtension(new CompilerTestExtension)->build();
    $template = $environment->parseString($source);
    $compiledPath = temporaryCompiledTemplatePath();

    try {
        $environment->compile($template, $compiledPath);
        $compiled = require $compiledPath;

        expect(file_get_contents($compiledPath))
            ->not->toContain('new AssignTag(')
            ->not->toContain('new CaptureTag(')
            ->not->toContain('new ForTag(');

        foreach (['render', 'stream'] as $method) {
            $contexts = [];
            $outputs = [];
            foreach ([$template, $compiled] as $candidate) {
                $context = $environment->newRenderContext(data: $data);
                $output = $candidate->$method($context);
                $outputs[] = $method === 'stream' ? implode('', iterator_to_array($output)) : $output;
                $contexts[] = $context;
            }
            expect($outputs[1])->toBe($outputs[0]);
            expect($contexts[1]->get('saved'))->toEqual($contexts[0]->get('saved'));
            expect($contexts[1]->resourceLimits->getAssignScore())->toBe($contexts[0]->resourceLimits->getAssignScore());
            expect($contexts[1]->resourceLimits->getCumulativeAssignScore())->toBe($contexts[0]->resourceLimits->getCumulativeAssignScore());
            expect($contexts[1]->resourceLimits->getRenderScore())->toBe($contexts[0]->resourceLimits->getRenderScore());
        }
    } finally {
        @unlink($compiledPath);
    }
})->with([
    'raw array' => ['{% assign saved = items %}{{ saved[1] }}', ['items' => ['a', 'b']]],
    'filtered array' => ['{% assign saved = items | split: "," %}{{ saved[1] }}', ['items' => 'a,b']],
    'filtered lookup remains an evaluator' => ['{% assign saved = "ignored" | compiler_lookup %}{{ saved }}', ['target' => 'value']],
    'range' => ['{% assign saved = (1..3) %}{{ saved | join }}', []],
    'nil' => ['{% assign saved = nil %}{% if saved %}wrong{% else %}nil{% endif %}', []],
    'false' => ['{% assign saved = false %}{% if saved %}wrong{% else %}false{% endif %}', []],
    'nested capture' => ['{% capture saved %}a{% capture inner %}b{{ value }}{% endcapture %}{{ inner }}{% endcapture %}{{ saved }}', ['value' => 'c']],
    'capture and assign inside a loop persist in the active scope' => ['{% for i in (1..3) %}{% assign last = i %}{% capture saved %}{{ last }}{% endcapture %}{% endfor %}{{ last }}{{ saved }}', []],
]);

test('compiled assignments and captures enforce resource limits at the same point', function (string $source, array $data, int $limit) {
    $environment = EnvironmentFactory::new()->build();
    $template = $environment->parseString($source);
    $compiledPath = temporaryCompiledTemplatePath();

    try {
        $environment->compile($template, $compiledPath);
        $compiled = require $compiledPath;
        foreach (['render', 'stream'] as $method) {
            $results = [];
            foreach ([$template, $compiled] as $candidate) {
                $context = $environment->newRenderContext(data: $data, resourceLimits: new ResourceLimits(
                    assignScoreLimit: $limit,
                    cumulativeAssignScoreLimit: $limit,
                ));
                $error = null;
                try {
                    $output = $candidate->$method($context);
                    if ($method === 'stream') {
                        iterator_to_array($output);
                    }
                } catch (ResourceLimitException $exception) {
                    $error = $exception::class;
                }
                $results[] = [$error, $context->get('saved'), $context->resourceLimits->getAssignScore(), $context->resourceLimits->getRenderScore()];
            }
            expect($results[1])->toEqual($results[0]);
            if ($limit === 1) {
                expect($results[1][0])->toBe(ResourceLimitException::class);
            }
        }
    } finally {
        @unlink($compiledPath);
    }
})->with([
    'array' => ['{% assign saved = items %}', ['items' => ['a', 'b', 'c']]],
    'large range' => ['{% assign saved = (start..end) %}', ['start' => 1, 'end' => 10_000_000]],
    'overflow range' => ['{% assign saved = (start..end) %}', ['start' => PHP_INT_MIN, 'end' => PHP_INT_MAX]],
    'capture' => ['{% capture saved %}abc{% endcapture %}', []],
    'nested capture' => ['{% capture saved %}a{% capture inner %}bc{% endcapture %}{{ inner }}{% endcapture %}', []],
    'liquid capture' => ["{% liquid\ncapture saved\necho 'abc'\nendcapture\n%}", []],
])->with([1, 3, 4, 5]);

test('compiled assignment preserves generator laziness', function () {
    $environment = EnvironmentFactory::new()->build();
    $template = $environment->parseString('{% assign saved = value %}');
    $compiledPath = temporaryCompiledTemplatePath();

    try {
        $environment->compile($template, $compiledPath);
        $compiled = require $compiledPath;
        foreach (['render', 'stream'] as $method) {
            $consumed = 0;
            $value = (static function () use (&$consumed): Generator {
                $consumed++;
                yield 'value';
            })();
            $context = $environment->newRenderContext(data: ['value' => $value]);
            $output = $compiled->$method($context);
            if ($method === 'stream') {
                iterator_to_array($output);
            }
            expect($context->get('saved'))->toBe($value);
            expect($consumed)->toBe(0);
        }
    } finally {
        @unlink($compiledPath);
    }
});

test('native tag subclasses retain their runtime overrides when compiled', function () {
    $environment = EnvironmentFactory::new()
        ->registerTag(CustomCompilerTestAssignTag::class)
        ->registerTag(CustomCompilerTestCaptureTag::class)
        ->registerTag(CustomCompilerTestForTag::class)
        ->registerTag(CustomCompilerTestLiquidTag::class)
        ->build();
    $template = $environment->parseString("{% assign a = 1 %}{% capture b %}ignored{% endcapture %}{% for i in (1..2) %}ignored{% endfor %}{% liquid\necho 'ignored'\n%}");
    $compiledPath = temporaryCompiledTemplatePath();

    try {
        $environment->compile($template, $compiledPath);
        $compiled = require $compiledPath;
        expect($compiled->render($environment->newRenderContext()))->toBe('assigncaptureforliquid');
        expect(implode('', iterator_to_array($compiled->stream($environment->newRenderContext()))))->toBe('assigncapturestreamliquid-stream');
    } finally {
        @unlink($compiledPath);
    }
});

class CustomCompilerTestAssignTag extends \Keepsuit\Liquid\Tags\AssignTag
{
    public function render(RenderContext $context): string
    {
        return 'assign';
    }
}

class CustomCompilerTestLiquidTag extends \Keepsuit\Liquid\Tags\LiquidTag implements CanBeStreamed
{
    public function render(RenderContext $context): string
    {
        return 'liquid';
    }

    public function stream(RenderContext $context): Generator
    {
        yield 'liquid-stream';
    }
}

class CustomCompilerTestCaptureTag extends \Keepsuit\Liquid\Tags\CaptureTag
{
    public function render(RenderContext $context): string
    {
        return 'capture';
    }
}

class CustomCompilerTestForTag extends \Keepsuit\Liquid\Tags\ForTag
{
    public function render(RenderContext $context): string
    {
        return 'for';
    }

    public function stream(RenderContext $context): Generator
    {
        yield 'stream';
    }
}

test('exportable nodes are rebuilt with constructors instead of serialization', function () {
    $environment = EnvironmentFactory::new()->build();
    $template = $environment->parseString('{{ product.title | upcase }}{% if a > 1 %}x{% endif %}');
    $compiledPath = temporaryCompiledTemplatePath();

    try {
        $environment->compile($template, $compiledPath);

        expect(file_get_contents($compiledPath))
            ->not->toContain('new Variable(')
            ->not->toContain('new VariableLookup(')
            ->not->toContain('new Condition(')
            ->toContain('::compare(')
            ->not->toContain('\unserialize(')
            ->not->toContain('deepclone_from_array');

        /** @var CompiledTemplate $compiled */
        $compiled = require $compiledPath;
        $data = ['product' => ['title' => 'hat'], 'a' => 2];

        expect($compiled->render($environment->newRenderContext(data: $data)))
            ->toBe($template->render($environment->newRenderContext(data: $data)))
            ->toBe('HATx');
    } finally {
        @unlink($compiledPath);
    }
});

test('native condition chains compile without reconstructed objects', function () {
    $environment = EnvironmentFactory::new()->build();
    $template = $environment->parseString('{% if a > 1 and b %}yes{% else %}no{% endif %}');
    $compiledPath = temporaryCompiledTemplatePath();

    try {
        $environment->compile($template, $compiledPath);

        expect(file_get_contents($compiledPath))
            ->not->toContain('\unserialize(')
            ->not->toContain('Condition::chain(')
            ->not->toContain('private readonly mixed $value')
            ->toContain(' && ')
            ->not->toContain('deepclone_from_array')
            ->not->toContain('Symfony\Component\VarExporter');

        /** @var CompiledTemplate $compiled */
        $compiled = require $compiledPath;

        foreach ([['a' => 2, 'b' => true], ['a' => 2, 'b' => false], ['a' => 0, 'b' => true]] as $data) {
            expect($compiled->render($environment->newRenderContext(data: $data)))
                ->toBe($template->render($environment->newRenderContext(data: $data)));
            expect(implode('', iterator_to_array($compiled->stream($environment->newRenderContext(data: $data)))))
                ->toBe(implode('', iterator_to_array($template->stream($environment->newRenderContext(data: $data)))));
        }
    } finally {
        @unlink($compiledPath);
    }
});

class CompilerBodyAwareCondition extends Condition
{
    public function __construct() {}

    public function evaluate(RenderContext $context): bool
    {
        return $this->body?->render($context) === 'marker';
    }
}

class CompilerNestedVariableNode extends Node implements CanBeCompiled
{
    public function render(RenderContext $context): string
    {
        return (new Variable(new VariableLookup('value')))->render($context);
    }

    public function compile(CompilerContext $context): void
    {
        $context->subcompile(new Variable(new VariableLookup('value')));
    }
}

test('unbuffered compiled fragments distinguish suppressed errors from empty handler output', function (string $exceptionClass, array $expected) {
    $handler = new class implements \Keepsuit\Liquid\Contracts\LiquidErrorHandler
    {
        public function handle(Throwable $error): string
        {
            return '';
        }
    };
    $environment = EnvironmentFactory::new()->setErrorHandler($handler)->setRethrowErrors(false)->build();
    $template = $environment->parseString('');
    $template->root->body->pushChild(new CompilerNestedVariableNode);
    $path = temporaryCompiledTemplatePath();

    try {
        $environment->compile($template, $path);
        $compiled = require $path;
        $context = $environment->newRenderContext(data: ['value' => fn () => throw new $exceptionClass('test')]);
        expect(iterator_to_array($compiled->stream($context), false))->toBe($expected);
        expect($context->getErrors())->toHaveCount(1);
    } finally {
        @unlink($path);
    }
})->with([
    [\Keepsuit\Liquid\Exceptions\UndefinedVariableException::class, []],
    [RuntimeException::class, ['']],
]);

test('conditions with custom children or cycles retain their serialization fallback', function (bool $cyclic) {
    $environment = EnvironmentFactory::new()->build();
    $template = $environment->parseString('{% if enabled %}yes{% else %}no{% endif %}');
    $condition = $template->root->body->children()[0]->parseTreeVisitorChildren()[0];
    if ($cyclic) {
        $condition->and($condition);
    } else {
        $condition->and((new CompilerBodyAwareCondition)->body(new BodyNode([new Text('marker')])));
    }
    $path = temporaryCompiledTemplatePath();

    try {
        $environment->compile($template, $path);
        $compiled = require $path;
        expect(file_get_contents($path))->toContain('\unserialize(');
        foreach ([$template, $compiled] as $candidate) {
            $data = ['enabled' => ! $cyclic];
            expect($candidate->render($environment->newRenderContext(data: $data)))->toBe($cyclic ? 'no' : 'yes');
            expect(implode('', iterator_to_array($candidate->stream($environment->newRenderContext(data: $data)))))
                ->toBe($cyclic ? 'no' : 'yes');
        }
    } finally {
        @unlink($path);
    }
})->with([false, true]);

test('exported condition chains retain short circuiting and runtime operator changes', function () {
    $environment = EnvironmentFactory::new()->build();
    $template = $environment->parseString('{% if a == b and c or d %}yes{% else %}no{% endif %}');
    $path = temporaryCompiledTemplatePath();

    try {
        $environment->compile($template, $path);
        $compiled = require $path;
        foreach ([false, true, false] as $override) {
            if ($override) {
                Condition::registerOperator('==', fn () => false);
            } else {
                Condition::deleteOperator('==');
            }
            foreach ([$template, $compiled] as $candidate) {
                foreach ([false, true] as $stream) {
                    $events = [];
                    $data = [];
                    foreach (['a' => 1, 'b' => 1, 'c' => true, 'd' => true] as $key => $value) {
                        $data[$key] = function () use (&$events, $key, $value) {
                            $events[] = $key;

                            return $value;
                        };
                    }
                    $context = $environment->newRenderContext(data: $data);
                    expect($stream ? implode('', iterator_to_array($candidate->stream($context))) : $candidate->render($context))
                        ->toBe($override ? 'no' : 'yes');
                    expect($events)->toBe($override ? ['a', 'b'] : ['a', 'b', 'c']);
                }
            }
        }
    } finally {
        Condition::deleteOperator('==');
        @unlink($path);
    }
});

test('compiled comparisons normalize values before evaluating the next operand', function () {
    $environment = EnvironmentFactory::new()->build();
    $template = $environment->parseString('{% if left == right %}yes{% endif %}');
    $path = temporaryCompiledTemplatePath();

    try {
        $environment->compile($template, $path);
        $compiled = require $path;
        foreach ([$template, $compiled] as $candidate) {
            foreach (['render', 'stream'] as $mode) {
                $events = new ArrayObject;
                $data = [];
                foreach (['left', 'right'] as $name) {
                    $data[$name] = function () use ($events, $name) {
                        $events[] = $name.' lookup';

                        return new CompilerLiquidEventValue($events, $name);
                    };
                }
                $context = $environment->newRenderContext(data: $data);
                $output = $mode === 'render' ? $candidate->render($context) : implode('', iterator_to_array($candidate->stream($context)));
                expect($output)->toBe('yes');
                expect($events->getArrayCopy())->toBe(['left lookup', 'left value', 'right lookup', 'right value']);
            }
        }
    } finally {
        @unlink($path);
    }
});

test('compiled unknown operators retain runtime registration and errors', function () {
    $environment = EnvironmentFactory::new()->setRethrowErrors(false)->build();
    $template = $environment->parseString('{% if value %}yes{% else %}no{% endif %}');
    $tag = $template->root->body->children()[0];
    $original = $tag->parseTreeVisitorChildren()[0];
    $condition = (new Condition(new VariableLookup('value'), 'compiler_custom', 1))->body($original->body);
    (new ReflectionProperty($tag, 'conditions'))->setValue($tag, [$condition, $tag->parseTreeVisitorChildren()[1]]);
    $path = temporaryCompiledTemplatePath();

    try {
        $environment->compile($template, $path);
        $compiled = require $path;
        foreach ([true, false] as $registered) {
            if ($registered) {
                Condition::registerOperator('compiler_custom', fn ($left, $right) => $left === $right);
            } else {
                Condition::deleteOperator('compiler_custom');
            }
            foreach ([$template, $compiled] as $candidate) {
                foreach (['render', 'stream'] as $mode) {
                    $context = $environment->newRenderContext(data: ['value' => 1]);
                    $output = $mode === 'render' ? $candidate->render($context) : implode('', iterator_to_array($candidate->stream($context)));
                    if ($registered) {
                        expect($output)->toBe('yes');
                    }
                    expect($context->getErrors())->toHaveCount($registered ? 0 : 1);
                }
            }
        }
    } finally {
        Condition::deleteOperator('compiler_custom');
        @unlink($path);
    }
});

test('native compiled ifchanged preserves output registers and resource scores', function (string $source) {
    $environment = EnvironmentFactory::new()->addExtension(new CompilerTestExtension)->setRethrowErrors(false)->build();
    $template = $environment->parseString($source);
    $path = temporaryCompiledTemplatePath();

    try {
        $environment->compile($template, $path);
        $compiled = require $path;
        expect(file_get_contents($path))->not->toContain('unserialize(');
        foreach (['render', 'stream'] as $mode) {
            $contexts = [];
            $outputs = [];
            foreach ([$template, $compiled] as $candidate) {
                $context = $environment->newRenderContext(data: ['items' => [1, 1, 2, 2, 1]]);
                $contexts[] = $context;
                $outputs[] = $mode === 'render' ? $candidate->render($context) : implode('', iterator_to_array($candidate->stream($context)));
            }
            expect($outputs[1])->toBe($outputs[0]);
            expect($contexts[1]->getRegister('ifchanged'))->toBe($contexts[0]->getRegister('ifchanged'));
            expect($contexts[1]->resourceLimits->getRenderScore())->toBe($contexts[0]->resourceLimits->getRenderScore());
            expect(count($contexts[1]->getErrors()))->toBe(count($contexts[0]->getErrors()));
        }
    } finally {
        @unlink($path);
    }
})->with([
    '{% for i in items %}{% ifchanged %}{{ i }}{% endifchanged %}{% endfor %}',
    '{% ifchanged %}x{% endifchanged %}{% ifchanged %}x{% endifchanged %}{% ifchanged %}y{% endifchanged %}',
    '{% ifchanged %}{{ "x" | compiler_generator }}{% endifchanged %}',
    '{% ifchanged %}before{{ "x" | unknown }}after{% endifchanged %}',
    '{% for i in items %}{% ifchanged %}{{ i }}{% break %}skip{% endifchanged %}skip{% endfor %}',
]);

test('ifchanged subclasses retain stream overrides and disabled checks', function (bool $disabled) {
    $environment = EnvironmentFactory::new()->registerTag(CompilerCustomIfChanged::class)->setRethrowErrors(false)->build();
    $template = $environment->parseString('{% ifchanged %}ignored{% endifchanged %}');
    $path = temporaryCompiledTemplatePath();

    try {
        $environment->compile($template, $path);
        $compiled = require $path;
        foreach ([$template, $compiled] as $candidate) {
            foreach (['render', 'stream'] as $mode) {
                $context = $environment->newRenderContext();
                $output = $context->withDisabledTags($disabled ? ['ifchanged'] : [], fn () => $mode === 'render'
                    ? $candidate->render($context)
                    : implode('', iterator_to_array($candidate->stream($context))));
                if (! $disabled) {
                    expect($output)->toBe($mode === 'render' ? 'override' : 'firstsecond');
                }
                expect($context->getErrors())->toHaveCount($disabled ? 1 : 0);
            }
        }
    } finally {
        @unlink($path);
    }
})->with([false, true]);

test('compiled tablerow preserves markup scopes interrupts and resource scores', function (string $source) {
    $environment = EnvironmentFactory::new()->setRethrowErrors(false)->build();
    $template = $environment->parseString($source);
    $path = temporaryCompiledTemplatePath();

    try {
        $environment->compile($template, $path);
        $compiled = require $path;
        expect(file_get_contents($path))->not->toContain('unserialize(');
        foreach (['render', 'stream'] as $mode) {
            $contexts = [];
            $outputs = [];
            foreach ([$template, $compiled] as $candidate) {
                $context = $environment->newRenderContext(data: ['items' => [1, 2, 3, 4], 'i' => 'outer', 'tablerowloop' => 'outer loop']);
                $contexts[] = $context;
                $outputs[] = $mode === 'render' ? $candidate->render($context) : implode('', iterator_to_array($candidate->stream($context)));
                expect($context->get('i'))->toBe('outer');
                expect($context->get('tablerowloop'))->toBe('outer loop');
            }
            expect($outputs[1])->toBe($outputs[0]);
            expect($contexts[1]->resourceLimits->getRenderScore())->toBe($contexts[0]->resourceLimits->getRenderScore());
            expect(count($contexts[1]->getErrors()))->toBe(count($contexts[0]->getErrors()));
        }
    } finally {
        @unlink($path);
    }
})->with([
    '{% tablerow i in items cols:2 %}{{ i | plus: 1 }}{% endtablerow %}',
    '{% tablerow i in (1..1000000000) cols:1 offset:-2 limit:1 %}{{ i }}{% endtablerow %}',
    '{% tablerow i in items cols:2 %}{{ tablerowloop.row }}:{{ tablerowloop.col }}{% endtablerow %}',
    '{% tablerow i in items cols:2 %}{{ i }}{% break %}skip{% endtablerow %}',
    '{% tablerow i in items cols:2 %}{% if i == 2 %}{% continue %}{% endif %}{{ i }}{% endtablerow %}',
    '{% tablerow i in items cols:2 %}{{ i | unknown }}{% endtablerow %}',
    '{% tablerow i in nil %}ignored{% endtablerow %}',
    '{% tablerow i in items limit:0 %}ignored{% endtablerow %}',
]);

test('compiled variables preserve interrupts raised by evaluators or present on entry', function (string $source, bool $incoming) {
    $environment = EnvironmentFactory::new()->build();
    $template = $environment->parseString($source);
    $path = temporaryCompiledTemplatePath();

    try {
        $environment->compile($template, $path);
        $compiled = require $path;
        foreach ([$template, $compiled] as $candidate) {
            foreach (['render', 'stream'] as $mode) {
                $context = $environment->newRenderContext(data: ['value' => new CompilerInterruptValue]);
                if ($incoming) {
                    $context->pushInterrupt(new \Keepsuit\Liquid\Interrupts\BreakInterrupt);
                }
                $output = $mode === 'render' ? $candidate->render($context) : implode('', iterator_to_array($candidate->stream($context)));
                expect($output)->toBe('x');
            }
        }
    } finally {
        @unlink($path);
    }
})->with([
    ['{{ "x" }}tail', true],
    ['{{ value }}tail', false],
    ['{% for i in (1..2) %}{{ value }}tail{% endfor %}', false],
]);

test('tablerow subclasses retain stream overrides and disabled checks', function (bool $disabled) {
    $environment = EnvironmentFactory::new()->registerTag(CompilerCustomTableRow::class)->setRethrowErrors(false)->build();
    $template = $environment->parseString('{% tablerow i in (1..2) %}ignored{% endtablerow %}');
    $path = temporaryCompiledTemplatePath();

    try {
        $environment->compile($template, $path);
        $compiled = require $path;
        foreach ([$template, $compiled] as $candidate) {
            foreach (['render', 'stream'] as $mode) {
                $context = $environment->newRenderContext();
                $output = $context->withDisabledTags($disabled ? ['tablerow'] : [], fn () => $mode === 'render'
                    ? $candidate->render($context)
                    : implode('', iterator_to_array($candidate->stream($context))));
                if (! $disabled) {
                    expect($output)->toBe($mode === 'render' ? 'override' : 'firstsecond');
                }
                expect($context->getErrors())->toHaveCount($disabled ? 1 : 0);
            }
        }
    } finally {
        @unlink($path);
    }
})->with([false, true]);

test('empty tablerow still enforces the nesting limit', function () {
    $environment = EnvironmentFactory::new()->setRethrowErrors(true)->build();
    $template = $environment->parseString('{% tablerow i in items %}unused{% endtablerow %}');
    $path = temporaryCompiledTemplatePath();

    try {
        $environment->compile($template, $path);
        $compiled = require $path;
        foreach ([$template, $compiled] as $candidate) {
            foreach (['render', 'stream'] as $mode) {
                $context = $environment->newRenderContext(data: ['items' => []]);
                for ($depth = 1; $depth < \Keepsuit\Liquid\Parse\ParseContext::MAX_DEPTH; $depth++) {
                    $context->enterScope();
                }
                expect(fn () => $mode === 'render' ? $candidate->render($context) : iterator_to_array($candidate->stream($context)))
                    ->toThrow(\Keepsuit\Liquid\Exceptions\StackLevelException::class);
            }
        }
    } finally {
        @unlink($path);
    }
});

test('constant raw nodes retain interrupts present on entry', function () {
    $environment = EnvironmentFactory::new()->build();
    $template = $environment->parseString('');
    $template->root->body->setChildren([new Raw('x'), new Text('tail')]);
    $path = temporaryCompiledTemplatePath();

    try {
        $environment->compile($template, $path);
        $compiled = require $path;
        foreach ([$template, $compiled] as $candidate) {
            foreach (['render', 'stream'] as $mode) {
                $context = $environment->newRenderContext();
                $context->pushInterrupt(new \Keepsuit\Liquid\Interrupts\BreakInterrupt);
                $output = $mode === 'render' ? $candidate->render($context) : implode('', iterator_to_array($candidate->stream($context)));
                expect($output)->toBe('x');
            }
        }
    } finally {
        @unlink($path);
    }
});

test('native rendered tags retain custom body rendering', function (string $source, string $expected) {
    $environment = EnvironmentFactory::new()->build();
    $template = $environment->parseString($source);
    $tag = $template->root->body->children()[0];
    (new ReflectionProperty($tag, 'body'))->setValue($tag, new CompilerCustomRenderedBody);
    $path = temporaryCompiledTemplatePath();

    try {
        $environment->compile($template, $path);
        $compiled = require $path;
        foreach ([$template, $compiled] as $candidate) {
            expect($candidate->render($environment->newRenderContext()))->toBe($expected);
            expect(implode('', iterator_to_array($candidate->stream($environment->newRenderContext()))))->toBe($expected);
        }
    } finally {
        @unlink($path);
    }
})->with([
    ['{% ifchanged %}ignored{% endifchanged %}', 'custom body'],
    ['{% tablerow i in (1..1) %}ignored{% endtablerow %}', "<tr class=\"row1\">\n<td class=\"col1\">custom body</td></tr>\n"],
]);

test('compiled rendered bodies keep distinct class identities and error locations', function () {
    $environment = EnvironmentFactory::new()->setStrictFilters(true)->setRethrowErrors(false)->build();
    $paths = [];

    try {
        $templates = [];
        foreach (['first', 'second'] as $value) {
            $template = $environment->parseString('{% ifchanged %}'.$value.'{% endifchanged %}');
            $path = temporaryCompiledTemplatePath();
            $paths[] = $path;
            $environment->compile($template, $path);
            $templates[] = require $path;
        }
        expect($templates[0]::class)->not->toBe($templates[1]::class);
        foreach ($templates as $index => $candidate) {
            expect($candidate->render($environment->newRenderContext()))->toBe($index === 0 ? 'first' : 'second');
        }

        $source = "{% ifchanged %}\n{{ 1 | unknown }}\n{% endifchanged %}\n{% ifchanged %}\n{{ 1 | unknown }}\n{% endifchanged %}";
        $template = $environment->parseString($source);
        $path = temporaryCompiledTemplatePath();
        $paths[] = $path;
        $environment->compile($template, $path);
        $compiled = require $path;
        foreach ([$template, $compiled] as $candidate) {
            foreach (['render', 'stream'] as $mode) {
                $context = $environment->newRenderContext();
                $mode === 'render' ? $candidate->render($context) : iterator_to_array($candidate->stream($context));
                expect(array_map(fn ($error) => $error->lineNumber, $context->getErrors()))->toBe([2, 5]);
            }
        }
    } finally {
        foreach ($paths as $path) {
            @unlink($path);
        }
    }
});

test('compiled node errors preserve handler output suppression and metadata', function (string $exceptionClass, bool $suppressed) {
    $handler = new class implements \Keepsuit\Liquid\Contracts\LiquidErrorHandler
    {
        public array $seen = [];

        public function handle(Throwable $error): string
        {
            $this->seen[] = [$error->lineNumber, $error->templateName];

            return '[handled]';
        }
    };
    $environment = EnvironmentFactory::new()->setErrorHandler($handler)->setRethrowErrors(false)->build();
    $template = $environment->parseString("prefix\n{{ value }}tail", name: 'errors.liquid');
    $path = temporaryCompiledTemplatePath();

    try {
        $environment->compile($template, $path);
        $compiled = require $path;
        foreach ([$template, $compiled] as $candidate) {
            foreach ([false, true] as $stream) {
                $handler->seen = [];
                $context = $environment->newRenderContext(data: ['value' => fn () => throw new $exceptionClass('test')]);
                expect($stream ? implode('', iterator_to_array($candidate->stream($context))) : $candidate->render($context))
                    ->toBe("prefix\n".($suppressed ? '' : '[handled]').'tail');
                expect($handler->seen)->toBe([[2, null]]);
                expect($context->getErrors())->toHaveCount(1);
            }
        }
    } finally {
        @unlink($path);
    }
})->with([
    [\Keepsuit\Liquid\Exceptions\UndefinedVariableException::class, true],
    [\Keepsuit\Liquid\Exceptions\UndefinedDropMethodException::class, true],
    [\Keepsuit\Liquid\Exceptions\UndefinedFilterException::class, true],
    [RuntimeException::class, false],
]);

test('exported nodes keep the state rendering depends on', function (string $source, array $data) {
    $environment = EnvironmentFactory::new()->build();
    $template = $environment->parseString($source);
    $compiledPath = temporaryCompiledTemplatePath();

    try {
        $environment->compile($template, $compiledPath);

        /** @var CompiledTemplate $compiled */
        $compiled = require $compiledPath;

        expect($compiled->render($environment->newRenderContext(data: $data)))
            ->toBe($template->render($environment->newRenderContext(data: $data)));
    } finally {
        @unlink($compiledPath);
    }
})->with([
    'nested lookups' => ['{{ a.b.c }}', ['a' => ['b' => ['c' => 'deep']]]],
    'indexed lookup' => ['{{ a[0].b }}', ['a' => [['b' => 'idx']]]],
    'dynamic lookup key' => ['{{ a[k] }}', ['a' => ['x' => 'dyn'], 'k' => 'x']],
    'filter with lookup argument' => ['{{ a | append: b }}', ['a' => 'x', 'b' => 'y']],
    'filter with named arguments' => ['{{ n | default: d, allow_false: true }}', ['n' => null, 'd' => 'fallback']],
    'range lookup' => ['{% for i in (a..b) %}{{ i }}{% endfor %}', ['a' => 1, 'b' => 3]],
    'literal in condition' => ['{% if a == empty %}e{% else %}f{% endif %}', ['a' => []]],
    'else condition' => ['{% case a %}{% when 1 %}one{% else %}other{% endcase %}', ['a' => 9]],
]);

test('compiled conditions preserve handled evaluation errors', function () {
    $environment = EnvironmentFactory::new()
        ->setRethrowErrors(false)
        ->build();
    $template = $environment->parseString('{% if "a" > 1 %}yes{% else %}no{% endif %}', name: 'condition-errors.liquid');
    $compiledPath = temporaryCompiledTemplatePath();

    try {
        $environment->compile($template, $compiledPath);

        /** @var CompiledTemplate $compiled */
        $compiled = require $compiledPath;
        $interpretedContext = $environment->newRenderContext();
        $compiledContext = $environment->newRenderContext();

        expect($compiled->render($compiledContext))
            ->toBe($template->render($interpretedContext))
            ->toBe('Liquid error (line 1): Internal exception');
        expect($compiled->getErrors()[0]->lineNumber)->toBe(1);
        expect($compiled->getErrors()[0]->templateName)
            ->toBe($template->getErrors()[0]->templateName);
    } finally {
        @unlink($compiledPath);
    }
});

test('compiled rendering preserves state across repeated renders', function () {
    $environment = EnvironmentFactory::new()->build();
    $template = $environment->parseString('{{ value }}{% assign value = "one" %}{{ value }}');
    $compiledPath = temporaryCompiledTemplatePath();

    try {
        $environment->compile($template, $compiledPath);

        /** @var CompiledTemplate $compiled */
        $compiled = require $compiledPath;
        $interpretedContext = $environment->newRenderContext();
        $compiledContext = $environment->newRenderContext();

        expect([
            $template->render($interpretedContext),
            $template->render($interpretedContext),
        ])->toBe([
            $compiled->render($compiledContext),
            $compiled->render($compiledContext),
        ])->toBe(['one', 'oneone']);
    } finally {
        @unlink($compiledPath);
    }
});

test('compiled rendering preserves collected errors and exception metadata', function () {
    $environment = EnvironmentFactory::new()
        ->setStrictVariables(true)
        ->setRethrowErrors(false)
        ->build();
    $template = $environment->parseString('{{ missing }}', name: 'errors.liquid');
    $compiledPath = temporaryCompiledTemplatePath();

    try {
        $environment->compile($template, $compiledPath);

        /** @var CompiledTemplate $compiled */
        $compiled = require $compiledPath;
        $interpretedContext = $environment->newRenderContext();
        $compiledContext = $environment->newRenderContext();

        expect($compiled->render($compiledContext))->toBe($template->render($interpretedContext));

        $describeErrors = static fn (Template $rendered): array => array_map(
            static fn (\Throwable $error): array => [
                $error::class,
                $error->getMessage(),
                $error->lineNumber,
                $error->templateName,
            ],
            $rendered->getErrors(),
        );

        expect($describeErrors($compiled))->toBe($describeErrors($template))
            ->toBe([[
                \Keepsuit\Liquid\Exceptions\UndefinedVariableException::class,
                'Variable `missing` not found',
                1,
                null,
            ]]);
    } finally {
        @unlink($compiledPath);
    }
});

test('compiled streaming continues after handled node errors', function () {
    $environment = EnvironmentFactory::new()
        ->setStrictVariables(true)
        ->setRethrowErrors(false)
        ->build();
    $template = $environment->parseString('a{{ missing }}b{{ also_missing }}c', name: 'stream-errors.liquid');
    $compiledPath = temporaryCompiledTemplatePath();

    try {
        $environment->compile($template, $compiledPath);

        /** @var CompiledTemplate $compiled */
        $compiled = require $compiledPath;
        $context = $environment->newRenderContext();

        expect(implode('', iterator_to_array($compiled->stream($context))))
            ->toBe('abc')
            ->and($compiled->getErrors())->toHaveCount(2);
    } finally {
        @unlink($compiledPath);
    }
});

test('compiled rendering attaches template metadata to rethrown exceptions', function () {
    $environment = EnvironmentFactory::new()
        ->setStrictVariables(true)
        ->setRethrowErrors(true)
        ->build();
    $template = $environment->parseString('{{ missing }}', name: 'errors.liquid');
    $compiledPath = temporaryCompiledTemplatePath();

    try {
        $environment->compile($template, $compiledPath);

        /** @var CompiledTemplate $compiled */
        $compiled = require $compiledPath;
        $exceptions = [];

        foreach ([$template, $compiled] as $candidate) {
            try {
                $candidate->render($environment->newRenderContext());
            } catch (\Keepsuit\Liquid\Exceptions\LiquidException $exception) {
                $exceptions[] = [
                    $exception::class,
                    $exception->lineNumber,
                    $exception->templateName,
                ];
            }
        }

        expect($exceptions)->toBe([
            [
                \Keepsuit\Liquid\Exceptions\UndefinedVariableException::class,
                1,
                'errors.liquid',
            ],
            [
                \Keepsuit\Liquid\Exceptions\UndefinedVariableException::class,
                1,
                'errors.liquid',
            ],
        ]);
    } finally {
        @unlink($compiledPath);
    }
});

test('compiled rendering preserves resource-limit exceptions', function () {
    $environment = EnvironmentFactory::new()->build();
    $template = $environment->parseString('0123456789', name: 'limited.liquid');
    $compiledPath = temporaryCompiledTemplatePath();

    try {
        $environment->compile($template, $compiledPath);

        /** @var CompiledTemplate $compiled */
        $compiled = require $compiledPath;
        $interpretedContext = $environment->newRenderContext(
            resourceLimits: new ResourceLimits(renderLengthLimit: 9),
        );
        $compiledContext = $environment->newRenderContext(
            resourceLimits: new ResourceLimits(renderLengthLimit: 9),
        );

        expect(fn () => $template->render($interpretedContext))
            ->toThrow(ResourceLimitException::class);
        expect(fn () => $compiled->render($compiledContext))
            ->toThrow(ResourceLimitException::class);
        expect($compiledContext->resourceLimits->reached())
            ->toBe($interpretedContext->resourceLimits->reached())
            ->toBeTrue();
    } finally {
        @unlink($compiledPath);
    }
});

test('compiled bodies preserve root and nested render-score accounting', function (string $source) {
    $environment = EnvironmentFactory::new()->build();
    $template = $environment->parseString($source);
    $compiledPath = temporaryCompiledTemplatePath();

    try {
        $environment->compile($template, $compiledPath);

        /** @var CompiledTemplate $compiled */
        $compiled = require $compiledPath;
        $interpretedContext = $environment->newRenderContext(
            data: ['enabled' => true, 'name' => 'value'],
            resourceLimits: new ResourceLimits(renderScoreLimit: 3),
        );
        $compiledContext = $environment->newRenderContext(
            data: ['enabled' => true, 'name' => 'value'],
            resourceLimits: new ResourceLimits(renderScoreLimit: 3),
        );

        expect(fn () => $template->render($interpretedContext))
            ->toThrow(ResourceLimitException::class);
        expect(fn () => $compiled->render($compiledContext))
            ->toThrow(ResourceLimitException::class);
        expect($compiledContext->resourceLimits->getRenderScore())
            ->toBe($interpretedContext->resourceLimits->getRenderScore())
            ->toBe(4);
    } finally {
        @unlink($compiledPath);
    }
})->with([
    'conditional body' => '{% if enabled %}a{{ name }}b{% endif %}',
    'liquid body' => "{% liquid\necho 'a'\necho name\necho 'b'\n%}",
]);

test('compiled rendering emits safe core nodes directly', function () {
    $environment = EnvironmentFactory::new()->build();
    $template = $environment->parseString('Hello {{ name | upcase }}!');
    $compiledPath = temporaryCompiledTemplatePath();

    try {
        $environment->compile($template, $compiledPath);

        $compiledSource = file_get_contents($compiledPath);

        expect($compiledSource)
            ->toContain('final class Template_')
            ->toContain('use Keepsuit\\Liquid\\Compiler\\CompiledTemplate;')
            ->toContain('extends CompiledTemplate')
            ->toContain('protected function renderCompiled')
            ->not->toContain('unserialize')
            ->not->toContain('return new CompiledTemplate(');

        /** @var CompiledTemplate $compiled */
        $compiled = require $compiledPath;

        expect($compiled->render($environment->newRenderContext(data: ['name' => 'World'])))
            ->toBe('Hello WORLD!');
    } finally {
        @unlink($compiledPath);
    }
});

test('storefront specs compile into readable direct output', function () {
    $environment = StorefrontTheme::environment();
    $template = $environment->parseTemplate('snippets.product.specs');
    $data = StorefrontTheme::renderData('templates.product')['page'];
    $interpreted = $template->render($environment->newRenderContext(staticData: $data));
    $compiledPath = temporaryCompiledTemplatePath();

    try {
        $environment->compile($template, $compiledPath);
        $compiledSource = file_get_contents($compiledPath);

        expect($compiledSource)
            ->toContain('use Keepsuit\\Liquid\\Compiler\\CompiledTemplate;')
            ->toContain('use Keepsuit\\Liquid\\Render\\RenderContext;')
            ->toContain('extends CompiledTemplate')
            ->not->toContain('new Variable(')
            ->toContain('// line 4')
            ->toContain("'size'")
            ->not->toContain('private readonly mixed $value')
            ->not->toContain('yield from [];')
            ->toContain('if ($context->hasInterrupt()) {');

        /** @var CompiledTemplate $compiled */
        $compiled = require $compiledPath;
        $compiledOutput = $compiled->render($environment->newRenderContext(staticData: $data));

        expect($compiledOutput)->toBe($interpreted);
        expect(implode('', iterator_to_array($compiled->stream($environment->newRenderContext(staticData: $data)))))
            ->toBe($interpreted);

        /** @var CompiledTemplate $secondCompiled */
        $secondCompiled = require $compiledPath;
        expect($secondCompiled)->toBeInstanceOf(CompiledTemplate::class);
    } finally {
        @unlink($compiledPath);
    }
});

test('complex compiled variables stream directly', function () {
    $environment = EnvironmentFactory::new()->build();
    $template = $environment->parseString('{{ values[key] }}');
    $compiledPath = temporaryCompiledTemplatePath();
    $data = ['values' => ['sku' => 'ABC'], 'key' => 'sku'];

    try {
        $environment->compile($template, $compiledPath);
        $compiledSource = file_get_contents($compiledPath);

        expect($compiledSource)
            ->toContain("new VariableLookup('key', [])")
            ->toContain('private readonly mixed $value0');

        /** @var CompiledTemplate $compiled */
        $compiled = require $compiledPath;

        expect($compiled->render($environment->newRenderContext(data: $data)))
            ->toBe($template->render($environment->newRenderContext(data: $data)));
    } finally {
        @unlink($compiledPath);
    }
});

test('direct variable emission preserves common Liquid values', function (string $source, array $data, string $expected) {
    $environment = EnvironmentFactory::new()->build();
    $template = $environment->parseString($source);
    $compiledPath = temporaryCompiledTemplatePath();

    try {
        $environment->compile($template, $compiledPath);

        expect(file_get_contents($compiledPath))
            ->not->toContain('new Variable(')
            ->toContain('::evaluate')
            ->not->toContain('private readonly mixed $value');

        /** @var CompiledTemplate $compiled */
        $compiled = require $compiledPath;
        $context = $environment->newRenderContext(data: $data);

        expect($compiled->render($context))->toBe($expected);
        expect($compiled->render($environment->newRenderContext(data: $data)))
            ->toBe($template->render($environment->newRenderContext(data: $data)));
    } finally {
        @unlink($compiledPath);
    }
})->with([
    'plain lookup' => ['{{ name }}', ['name' => 'World'], 'World'],
    'nested lookup' => ['{{ product.title }}', ['product' => ['title' => 'Hat']], 'Hat'],
    'size filter' => ['{{ items | size }}', ['items' => [1, 2, 3]], '3'],
    'scalar filter argument' => ['{{ value | append: 2 }}', ['value' => 'x'], 'x2'],
    'renderable value' => ['{{ value }}', ['value' => new Text('rendered')], 'rendered'],
]);

test('storefront header compiles static partial rendering with direct values', function () {
    $environment = StorefrontTheme::environment();
    $template = $environment->parseTemplate('snippets.page.header');
    $data = [
        'shop' => ['name' => 'Field Goods'],
        'page' => ['title' => 'About'],
    ];
    $interpreted = $template->render($environment->newRenderContext(staticData: $data));
    $compiledPath = temporaryCompiledTemplatePath();

    try {
        $environment->compile($template, $compiledPath);
        $compiledSource = file_get_contents($compiledPath);

        expect($compiledSource)
            ->not->toContain('new Variable(')
            ->toContain("::evaluateParts(\$context, 'shop', ['name'])")
            ->toContain('yieldPartial')
            ->not->toContain('yield from [];')
            ->not->toContain('private readonly mixed $value');

        /** @var CompiledTemplate $compiled */
        $compiled = require $compiledPath;

        expect($compiled->render($environment->newRenderContext(staticData: $data)))->toBe($interpreted);
    } finally {
        @unlink($compiledPath);
    }
});

test('compiler context writes indented output statements', function () {
    $context = new CompilerContext;

    $context->write('function generated() {');
    $context->indent();
    $context->raw("if (true) {\nreturn true;\n}");
    $context->outdent();
    $context->write('}');

    expect($context->getSource())
        ->toBe("function generated() {\nif (true) {\nreturn true;\n}\n}\n");
});

test('compiler and code builder writer methods are fluent', function () {
    $builder = new CodeBuilder;

    expect($builder->indent())->toBe($builder);
    expect($builder->writeLine('builder line'))->toBe($builder);
    expect($builder->writeRaw("\nbuilder raw"))->toBe($builder);
    expect($builder->dedent())->toBe($builder);

    $checkpoint = $builder->checkpoint();

    expect($builder->writeLine('discarded'))->toBe($builder);
    expect($builder->rollback($checkpoint))->toBe($builder);

    $context = new CompilerContext($builder);

    expect($context->write('context line'))->toBe($context);
    expect($context->raw('context raw'))->toBe($context);
    expect($context->indent())->toBe($context);
    expect($context->writeOutput($context->writeValue('output')))->toBe($context);
    expect($context->subcompile(new Text('child')))->toBe($context);
    expect($context->outdent())->toBe($context);
});

test('built-in compilable nodes implement the compiler contract directly', function () {
    $nodes = [
        new Text('text'),
        new Raw('raw'),
        new Variable('name'),
        new Document(new BodyNode),
        new BodyNode,
    ];

    foreach ($nodes as $node) {
        expect($node)->toBeInstanceOf(CanBeCompiled::class);
    }
});

test('compiled enums retain identity without serialization', function (UnitEnum $value) {
    $environment = EnvironmentFactory::new()->addExtension(new CompilerTestExtension)->build();
    $template = $environment->parseString('');
    $template->root->body->pushChild(new Variable($value, [['compiler_enum_identity', [], []]]));
    $path = temporaryCompiledTemplatePath();

    try {
        $environment->compile($template, $path);
        $compiled = require $path;
        expect(file_get_contents($path))->not->toContain('unserialize(');
        foreach ([$template, $compiled] as $candidate) {
            expect($candidate->render($environment->newRenderContext()))->toBe($value::class.'::'.$value->name);
            expect(implode('', iterator_to_array($candidate->stream($environment->newRenderContext()))))->toBe($value::class.'::'.$value->name);
        }
    } finally {
        @unlink($path);
    }
})->with([CompilerUnitEnum::Ready, CompilerBackedEnum::Ready, \Keepsuit\Liquid\Nodes\Literal::Empty, \Keepsuit\Liquid\Nodes\Literal::Blank]);

test('enum exporters retain precedence over native enum reconstruction', function () {
    $environment = EnvironmentFactory::new()->build();
    $template = $environment->parseString('');
    $template->root->body->pushChild(new Variable(CompilerExportedEnum::Ready));
    $path = temporaryCompiledTemplatePath();

    try {
        $environment->compile($template, $path);
        $compiled = require $path;
        foreach ([$template, $compiled] as $candidate) {
            expect($candidate->render($environment->newRenderContext()))->toBe('custom enum');
            expect(implode('', iterator_to_array($candidate->stream($environment->newRenderContext()))))->toBe('custom enum');
        }
    } finally {
        @unlink($path);
    }
});

test('compiled name lookups retain scope changes null values and strict missing errors', function (bool $strict) {
    $environment = EnvironmentFactory::new()->setStrictVariables($strict)->setRethrowErrors(false)->build();
    $template = $environment->parseString('{{ root }}|{{ missing }}|{% assign root = "changed" %}{{ root }}|{{ value | default: "none" }}');
    $path = temporaryCompiledTemplatePath();

    try {
        $environment->compile($template, $path);
        $compiled = require $path;
        expect(file_get_contents($path))->toContain('::evaluateName($context,')->not->toContain('::evaluateParts(');
        foreach ([$template, $compiled] as $candidate) {
            foreach (['render', 'stream'] as $mode) {
                $context = $environment->newRenderContext(data: ['root' => 'outer', 'value' => null]);
                $context->enterScope();
                $context->set('root', 'inner');
                try {
                    $output = $mode === 'render' ? $candidate->render($context) : implode('', iterator_to_array($candidate->stream($context)));
                    expect($output)->toBe('inner||inner|none');
                    expect($context->getErrors())->toHaveCount($strict ? 1 : 0);
                    expect($context->get('root'))->toBe('inner');
                } finally {
                    $context->leaveScope();
                }
                expect($context->get('root'))->toBe('changed');
            }
        }
    } finally {
        @unlink($path);
    }
})->with([false, true]);

test('compiled filter arguments fully evaluate lookups in positional and named order', function () {
    $environment = EnvironmentFactory::new()->addExtension(new CompilerTestExtension)->build();
    $template = $environment->parseString('{{ "x" | compiler_arguments: first, product[key], named: last }}');
    $path = temporaryCompiledTemplatePath();

    try {
        $environment->compile($template, $path);
        $compiled = require $path;
        expect(file_get_contents($path))
            ->not->toContain('[...[')
            ->not->toContain("new VariableLookup('first'")
            ->not->toContain("new VariableLookup('product'")
            ->not->toContain("new VariableLookup('last'");
        foreach ([$template, $compiled] as $candidate) {
            foreach (['render', 'stream'] as $mode) {
                $counter = new CompilerCountingPartialValue;
                $context = $environment->newRenderContext(data: [
                    'first' => new VariableLookup('counter'),
                    'product' => ['part' => new VariableLookup('counter')],
                    'key' => 'part',
                    'last' => new VariableLookup('counter'),
                    'counter' => $counter,
                ]);
                $output = $mode === 'render' ? $candidate->render($context) : implode('', iterator_to_array($candidate->stream($context)));
                expect($output)->toBe('["x",1,2,3]');
                expect($counter->calls)->toBe(3);
            }
        }
    } finally {
        @unlink($path);
    }
});

test('compiled filter argument arrays retain numeric reindexing and overwritten evaluations', function (array $positional, array $named, string $expected) {
    $environment = EnvironmentFactory::new()->addExtension(new CompilerTestExtension)->build();
    $template = $environment->parseString('');
    $arguments = array_map(fn () => new VariableLookup('counter'), $positional);
    $namedArguments = array_map(fn () => new VariableLookup('counter'), $named);
    $template->root->body->pushChild(new Variable('x', [['compiler_arguments', $arguments, $namedArguments]]));
    $path = temporaryCompiledTemplatePath();

    try {
        $environment->compile($template, $path);
        expect(file_get_contents($path))->not->toContain('[...[');
        $compiled = require $path;
        foreach ([$template, $compiled] as $candidate) {
            foreach (['render', 'stream'] as $method) {
                $counter = new CompilerCountingPartialValue;
                $output = $candidate->$method($environment->newRenderContext(data: ['counter' => $counter]));
                expect($method === 'render' ? $output : implode('', iterator_to_array($output)))->toBe($expected);
                expect($counter->calls)->toBe(count($positional) + count($named));
            }
        }
    } finally {
        @unlink($path);
    }
})->with([
    'sparse numeric keys' => [[8 => null, -2 => null], ['named' => null], '["x",1,2,3]'],
    'overwritten string key' => [['first' => null], ['first' => null, 'named' => null], '["x",2,null,3]'],
    'named only' => [[], ['first' => null, 'named' => null], '["x",1,null,2]'],
]);

test('compiled filter arguments preserve strict missing values and evaluation order', function (bool $strict, bool $named) {
    $environment = EnvironmentFactory::new()->addExtension(new CompilerTestExtension)->setStrictVariables($strict)->setRethrowErrors(false)->build();
    $source = $named
        ? '{{ "x" | compiler_arguments: first, second, named: missing }}'
        : '{{ "x" | compiler_arguments: first, missing, named: last }}';
    $template = $environment->parseString($source);
    $path = temporaryCompiledTemplatePath();

    try {
        $environment->compile($template, $path);
        $compiled = require $path;
        foreach ([$template, $compiled] as $candidate) {
            foreach (['render', 'stream'] as $mode) {
                $counter = new CompilerCountingPartialValue;
                $context = $environment->newRenderContext(data: ['first' => $counter, 'second' => $counter, 'last' => $counter]);
                $output = $mode === 'render' ? $candidate->render($context) : implode('', iterator_to_array($candidate->stream($context)));
                $missing = $strict ? '{"variableName":"missing"}' : 'null';
                expect($output)->toBe($named ? '["x",1,2,'.$missing.']' : '["x",1,'.$missing.',2]');
                expect($counter->calls)->toBe(2);
                expect($context->getErrors())->toHaveCount(0);
            }
        }
    } finally {
        @unlink($path);
    }
})->with([[false, false], [false, true], [true, false], [true, true]]);

test('compiled filter argument coercion preserves strict errors', function (bool $strict) {
    $environment = EnvironmentFactory::new()->setStrictVariables($strict)->setRethrowErrors(false)->build();
    $template = $environment->parseString('before{{ "x" | append: missing }}after');
    $path = temporaryCompiledTemplatePath();

    try {
        $environment->compile($template, $path);
        $compiled = require $path;
        foreach ([$template, $compiled] as $candidate) {
            foreach (['render', 'stream'] as $mode) {
                $context = $environment->newRenderContext();
                $output = $mode === 'render' ? $candidate->render($context) : implode('', iterator_to_array($candidate->stream($context)));
                expect($output)->toBe($strict ? 'beforeafter' : 'beforexafter');
                expect($context->getErrors())->toHaveCount($strict ? 1 : 0);
            }
        }
    } finally {
        @unlink($path);
    }
})->with([false, true]);

test('compiled filter arguments retain custom lookup evaluation', function () {
    $environment = EnvironmentFactory::new()->addExtension(new CompilerTestExtension)->build();
    $template = $environment->parseString('');
    $template->root->body->pushChild(new Variable('x', [['compiler_arguments', [new CompilerInheritedLookup('ignored')], ['named' => 'tail']]]));
    $path = temporaryCompiledTemplatePath();

    try {
        $environment->compile($template, $path);
        $compiled = require $path;
        foreach ([$template, $compiled] as $candidate) {
            expect($candidate->render($environment->newRenderContext()))->toBe('["x",[7,8],null,"tail"]');
            expect(implode('', iterator_to_array($candidate->stream($environment->newRenderContext()))))->toBe('["x",[7,8],null,"tail"]');
        }
    } finally {
        @unlink($path);
    }
});

test('compiled assignments resolve nested evaluators once', function () {
    $environment = EnvironmentFactory::new()->build();
    $template = $environment->parseString('{% assign result = value %}{{ result }}');
    $path = temporaryCompiledTemplatePath();

    try {
        $environment->compile($template, $path);
        $compiled = require $path;
        foreach ([$template, $compiled] as $candidate) {
            foreach (['render', 'stream'] as $mode) {
                $counter = new CompilerCountingPartialValue;
                $context = $environment->newRenderContext(data: ['value' => new VariableLookup('counter'), 'counter' => $counter]);
                $output = $mode === 'render' ? $candidate->render($context) : implode('', iterator_to_array($candidate->stream($context)));
                expect($output)->toBe('1');
                expect($counter->calls)->toBe(1);
                expect($context->get('result'))->toBe(1);
            }
        }
    } finally {
        @unlink($path);
    }
});

test('expression values remain exportable while variables compile directly', function () {
    $variable = new Variable('name');

    expect($variable)
        ->toBeInstanceOf(CanBeCompiled::class)
        ->toBeInstanceOf(CanBeExported::class);

    $values = [
        new VariableLookup('name'),
        new RangeLookup(1, 5),
        new Condition(1, '==', 1),
    ];

    foreach ($values as $value) {
        expect($value)->toBeInstanceOf(CanBeExported::class);
        expect($value)->not->toBeInstanceOf(CanBeCompiled::class);
    }
});

test('unsupported nodes use the interpreter fallback', function () {
    $environment = EnvironmentFactory::new()->build();
    $template = $environment->parseString('{% assign greeting = "Hello" %}{{ greeting }}');
    $compiledPath = temporaryCompiledTemplatePath();

    try {
        $environment->compile($template, $compiledPath);

        /** @var CompiledTemplate $compiled */
        $compiled = require $compiledPath;

        expect($compiled->render($environment->newRenderContext()))
            ->toBe('Hello');
    } finally {
        @unlink($compiledPath);
    }
});

test('template literals stay data when compiled', function () {
    $environment = EnvironmentFactory::new()->build();
    $literal = "before <?php echo 'unsafe'; ?> after";
    $template = $environment->parseString($literal);
    $compiledPath = temporaryCompiledTemplatePath();

    try {
        ob_start();
        $environment->compile($template, $compiledPath);

        /** @var CompiledTemplate $compiled */
        $compiled = require $compiledPath;
        $artifactOutput = ob_get_clean();

        expect($artifactOutput)->toBe('');

        expect($compiled->render($environment->newRenderContext()))
            ->toBe($template->render($environment->newRenderContext()));
    } finally {
        @unlink($compiledPath);
    }
});

test('compiled literals preserve quotes escapes and control characters', function () {
    $environment = EnvironmentFactory::new()->build();
    $literal = "quote ' and \"\nline\r\t\0 `backtick` <?php echo 'unsafe'; ?>";
    $template = $environment->parseString($literal);
    $compiledPath = temporaryCompiledTemplatePath();

    try {
        $environment->compile($template, $compiledPath);

        /** @var CompiledTemplate $compiled */
        $compiled = require $compiledPath;

        expect($compiled->render($environment->newRenderContext()))
            ->toBe($template->render($environment->newRenderContext()));
    } finally {
        @unlink($compiledPath);
    }
});

test('custom compilable nodes opt in through the compiler context', function () {
    $environment = EnvironmentFactory::new()->build();
    $template = $environment->parseString('prefix');
    assert($template instanceof ParsedTemplate);
    $template->root->body->pushChild(new CompilableCompilerTestNode('custom output'));
    $compiledPath = temporaryCompiledTemplatePath();

    try {
        $environment->compile($template, $compiledPath);

        /** @var CompiledTemplate $compiled */
        $compiled = require $compiledPath;

        expect($compiled->render($environment->newRenderContext()))
            ->toBe('prefixcustom output');
    } finally {
        @unlink($compiledPath);
    }
});

test('custom compilable tags opt in without changing tag registration', function () {
    $environment = EnvironmentFactory::new()->build();
    $template = $environment->parseString('prefix');
    assert($template instanceof ParsedTemplate);
    $template->root->body->pushChild(new CompilableCompilerTestTag);
    $compiledPath = temporaryCompiledTemplatePath();

    try {
        $environment->compile($template, $compiledPath);

        /** @var CompiledTemplate $compiled */
        $compiled = require $compiledPath;

        expect($compiled->render($environment->newRenderContext()))
            ->toBe('prefixtag output');
    } finally {
        @unlink($compiledPath);
    }
});

test('compiler extensions retain custom tag and filter registration', function () {
    $environment = EnvironmentFactory::new()
        ->addExtension(new CompilerTestExtension)
        ->build();
    $template = $environment->parseString('{% compiler_test %}{{ name | compiler_marker }}');
    $compiledPath = temporaryCompiledTemplatePath();

    expect($environment->tagRegistry->get('compiler_test'))
        ->toBe(CompilableCompilerTestTag::class);
    expect($environment->filterRegistry->has('compiler_marker'))->toBeTrue();

    try {
        $environment->compile($template, $compiledPath);

        /** @var CompiledTemplate $compiled */
        $compiled = require $compiledPath;

        expect($compiled->render($environment->newRenderContext(data: ['name' => 'value'])))
            ->toBe('tag outputfiltered value');
    } finally {
        @unlink($compiledPath);
    }
});

test('unsupported tags retain runtime filters and disabled-tag behavior', function () {
    $environment = EnvironmentFactory::new()
        ->addExtension(new CompilerTestExtension)
        ->build();
    $template = $environment->parseString('{% runtime_fallback %}', name: 'fallback.liquid');
    $compiledPath = temporaryCompiledTemplatePath();

    try {
        $environment->compile($template, $compiledPath);

        /** @var CompiledTemplate $compiled */
        $compiled = require $compiledPath;

        expect($compiled->render($environment->newRenderContext()))
            ->toBe($template->render($environment->newRenderContext()))
            ->toBe('filtered runtime');

        $renderDisabled = static function (Template $candidate, RenderContext $context): string {
            return $context->withDisabledTags(
                ['runtime_fallback'],
                fn () => $candidate->render($context),
            );
        };
        $interpretedContext = $environment->newRenderContext();
        $compiledContext = $environment->newRenderContext();

        expect($renderDisabled($compiled, $compiledContext))
            ->toBe($renderDisabled($template, $interpretedContext))
            ->toBe('Liquid error (line 1): runtime_fallback usage is not allowed in this context');
        expect($compiled->getErrors()[0]::class)
            ->toBe(\Keepsuit\Liquid\Exceptions\TagDisabledException::class);
        expect($compiled->getErrors()[0]->lineNumber)->toBe(1);
    } finally {
        @unlink($compiledPath);
    }
});

test('failed node compilation rolls back before runtime fallback', function () {
    $environment = EnvironmentFactory::new()->build();
    $template = $environment->parseString('prefix');
    assert($template instanceof ParsedTemplate);
    $template->root->body->pushChild(new FailingCompilableCompilerTestNode);
    $compiledPath = temporaryCompiledTemplatePath();
    FailingCompilableCompilerTestNode::$compilations = 0;

    try {
        $environment->compile($template, $compiledPath);

        expect(FailingCompilableCompilerTestNode::$compilations)->toBe(1);

        $compiledSource = file_get_contents($compiledPath);

        expect(str_contains($compiledSource ?: '', 'partial output'))->toBeFalse();
        expect(str_contains($compiledSource ?: '', 'discarded body'))->toBeFalse();

        /** @var CompiledTemplate $compiled */
        $compiled = require $compiledPath;

        expect($compiled->render($environment->newRenderContext()))
            ->toBe('prefixfallback output');
    } finally {
        @unlink($compiledPath);
    }
});

test('yieldless compiled nodes still use the node error boundary', function () {
    $environment = EnvironmentFactory::new()
        ->setRethrowErrors(false)
        ->build();
    $template = $environment->parseString('prefixsuffix', name: 'yieldless-node.liquid');
    assert($template instanceof ParsedTemplate);
    $template->root->body->setChildren([
        new Text('prefix'),
        (new RuntimeThrowingCompilableCompilerTestNode)->setLineNumber(7),
        new Text('suffix'),
    ]);
    $compiledPath = temporaryCompiledTemplatePath();

    try {
        $environment->compile($template, $compiledPath);

        $compiledSource = file_get_contents($compiledPath);

        expect($compiledSource)
            ->toContain('function () use ($context): iterable {')
            ->not->toContain('yield from [];');

        /** @var CompiledTemplate $compiled */
        $compiled = require $compiledPath;
        $interpretedContext = $environment->newRenderContext();
        $compiledContext = $environment->newRenderContext();

        expect($compiled->render($compiledContext))
            ->toBe($template->render($interpretedContext));
        expect($compiled->getErrors())->toHaveCount(1);
        expect($compiled->getErrors()[0]->lineNumber)
            ->toBe($template->getErrors()[0]->lineNumber)
            ->toBe(7);
    } finally {
        @unlink($compiledPath);
    }
});

test('small literal bodies fold buffered streaming without losing incoming interrupts', function () {
    $environment = EnvironmentFactory::new()->build();
    $template = $environment->parseString(str_repeat('{{ "text" }}', 128));
    $compiledPath = temporaryCompiledTemplatePath();

    try {
        $environment->compile($template, $compiledPath);
        $compiled = require $compiledPath;
        expect(file_get_contents($compiledPath))->not->toContain('catch (');
        foreach ([$template, $compiled] as $candidate) {
            $context = $environment->newRenderContext();
            expect(implode('', iterator_to_array($candidate->stream($context), false)))->toBe(str_repeat('text', 128));
            $context = $environment->newRenderContext();
            $context->pushInterrupt(new \Keepsuit\Liquid\Interrupts\BreakInterrupt);
            expect(implode('', iterator_to_array($candidate->stream($context), false)))->toBe('text');
        }
    } finally {
        @unlink($compiledPath);
    }
});

test('compiled node closures retain their error boundary', function () {
    $environment = EnvironmentFactory::new()->setRethrowErrors(false)->build();
    $template = $environment->parseString('beforeafter', name: 'node-error.liquid');
    $template->root->body->setChildren([
        new Text('before'),
        (new RuntimeThrowingCompilableCompilerTestNode)->setLineNumber(7),
        new Text('after'),
    ]);
    $path = temporaryCompiledTemplatePath();

    try {
        $environment->compile($template, $path);
        $compiled = require $path;
        expect(implode('', iterator_to_array($compiled->stream($environment->newRenderContext()), false)))
            ->toBe('beforeLiquid error (line 7): Internal exceptionafter');
        expect($compiled->getErrors())->toHaveCount(1);
        expect($compiled->getErrors()[0]->lineNumber)->toBe(7);
    } finally {
        @unlink($path);
    }
});

test('unexpected node compilation errors propagate without publishing an artifact', function () {
    $environment = EnvironmentFactory::new()->build();
    $template = $environment->parseString('prefix');
    $template->root->body->pushChild(new FailingCompilableCompilerTestNode(unsupported: false));
    $path = temporaryCompiledTemplatePath();

    try {
        expect(fn () => $environment->compile($template, $path))
            ->toThrow(RuntimeException::class, 'compiler test failure');
        expect(is_file($path))->toBeFalse();
    } finally {
        if (is_file($path)) {
            unlink($path);
        }
    }
});

test('compilation fails when a fallback node cannot be safely reconstructed', function () {
    $environment = EnvironmentFactory::new()->build();
    $template = $environment->parseString('prefix', name: 'unsafe.liquid');
    $resource = fopen('php://memory', 'r');

    if ($resource === false) {
        throw new RuntimeException('Unable to create a test resource.');
    }

    assert($template instanceof ParsedTemplate);
    $template->root->body->pushChild(
        (new UnsafeFallbackCompilerTestNode($resource))->setLineNumber(7),
    );
    $compiledPath = temporaryCompiledTemplatePath();

    try {
        expect(fn () => $environment->compile($template, $compiledPath))
            ->toThrow(
                RuntimeException::class,
                'Unable to safely reconstruct fallback node UnsafeFallbackCompilerTestNode at line 7 in template unsafe.liquid.',
            );
        expect($compiledPath)->not->toBeFile();
        expect($template->render($environment->newRenderContext()))
            ->toBe('prefixunsafe fallback');
    } finally {
        fclose($resource);

        if (is_file($compiledPath)) {
            unlink($compiledPath);
        }
    }
});
