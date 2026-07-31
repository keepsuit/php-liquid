<?php

use Keepsuit\Liquid\Compiler\CodeBuilder;
use Keepsuit\Liquid\Compiler\CompiledTemplate;
use Keepsuit\Liquid\Compiler\CompiledTemplateInterface;
use Keepsuit\Liquid\Compiler\CompilerContext;
use Keepsuit\Liquid\Contracts\CanBeCompiled;
use Keepsuit\Liquid\Contracts\Disableable;
use Keepsuit\Liquid\EnvironmentFactory;
use Keepsuit\Liquid\Exceptions\ResourceLimitException;
use Keepsuit\Liquid\Extensions\Extension;
use Keepsuit\Liquid\Filters\FiltersProvider;
use Keepsuit\Liquid\Nodes\BodyNode;
use Keepsuit\Liquid\Nodes\Document;
use Keepsuit\Liquid\Nodes\Node;
use Keepsuit\Liquid\Nodes\Raw;
use Keepsuit\Liquid\Nodes\Text;
use Keepsuit\Liquid\Nodes\Variable;
use Keepsuit\Liquid\Parse\TagParseContext;
use Keepsuit\Liquid\Render\RenderContext;
use Keepsuit\Liquid\Render\ResourceLimits;
use Keepsuit\Liquid\Tag;
use Keepsuit\Liquid\Template;
use Keepsuit\Liquid\TemplateInterface;

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
    public function render(RenderContext $context): string
    {
        return 'fallback output';
    }

    public function compile(CompilerContext $context): void
    {
        $context->writeOutput($context->writeValue('partial output'));

        throw new RuntimeException('compiler test failure');
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

function temporaryCompiledTemplatePath(): string
{
    $path = tempnam(sys_get_temp_dir(), 'liquid-compiled-');

    if ($path === false) {
        throw new RuntimeException('Unable to create a temporary compiled template path.');
    }

    unlink($path);

    return $path.'.php';
}

test('compiled render collects the compiled stream', function () {
    $compiled = new class extends CompiledTemplate
    {
        public function name(): ?string
        {
            return null;
        }

        protected function streamCompiled(RenderContext $context): Generator
        {
            yield 'stream body';
        }
    };

    expect($compiled->render(new RenderContext))->toBe('stream body');
});

test('environment compiles a template to a requireable artifact', function () {
    $environment = EnvironmentFactory::new()->build();
    $template = $environment->parseString('Hello {{ name }}');
    $compiledPath = temporaryCompiledTemplatePath();

    try {
        $environment->compile($template, $compiledPath);

        expect($compiledPath)->toBeFile();

        /** @var CompiledTemplateInterface $compiled */
        $compiled = require $compiledPath;

        expect($compiled)->toBeInstanceOf(CompiledTemplateInterface::class);
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

        expect($compiledSource)->toContain('->evaluate($context)');

        /** @var CompiledTemplateInterface $compiled */
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

test('compiled templates keep runtime partial lookup', function () {
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

        expect($compiledSource)->toContain('renderNode');

        /** @var CompiledTemplateInterface $compiled */
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

test('compiled rendering preserves state across repeated renders', function () {
    $environment = EnvironmentFactory::new()->build();
    $template = $environment->parseString('{{ value }}{% assign value = "one" %}{{ value }}');
    $compiledPath = temporaryCompiledTemplatePath();

    try {
        $environment->compile($template, $compiledPath);

        /** @var CompiledTemplateInterface $compiled */
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

        /** @var CompiledTemplateInterface $compiled */
        $compiled = require $compiledPath;
        $interpretedContext = $environment->newRenderContext();
        $compiledContext = $environment->newRenderContext();

        expect($compiled->render($compiledContext))->toBe($template->render($interpretedContext));

        $describeErrors = static fn (TemplateInterface $rendered): array => array_map(
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

test('compiled rendering attaches template metadata to rethrown exceptions', function () {
    $environment = EnvironmentFactory::new()
        ->setStrictVariables(true)
        ->setRethrowErrors(true)
        ->build();
    $template = $environment->parseString('{{ missing }}', name: 'errors.liquid');
    $compiledPath = temporaryCompiledTemplatePath();

    try {
        $environment->compile($template, $compiledPath);

        /** @var CompiledTemplateInterface $compiled */
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

        /** @var CompiledTemplateInterface $compiled */
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

test('compiled rendering emits safe core nodes directly', function () {
    $environment = EnvironmentFactory::new()->build();
    $template = $environment->parseString('Hello {{ name | upcase }}!');
    $compiledPath = temporaryCompiledTemplatePath();

    try {
        $environment->compile($template, $compiledPath);

        $compiledSource = file_get_contents($compiledPath);

        expect($compiledSource)
            ->toContain('final class Template_')
            ->toContain('extends \\Keepsuit\\Liquid\\Compiler\\CompiledTemplate')
            ->toContain('protected function streamCompiled')
            ->not->toContain('unserialize')
            ->not->toContain('return new \\Keepsuit\\Liquid\\Compiler\\CompiledTemplate(');

        /** @var CompiledTemplateInterface $compiled */
        $compiled = require $compiledPath;

        expect($compiled->render($environment->newRenderContext(data: ['name' => 'World'])))
            ->toBe('Hello WORLD!');
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
        new Document(new BodyNode),
        new BodyNode,
        new Variable('name'),
    ];

    foreach ($nodes as $node) {
        expect($node)->toBeInstanceOf(CanBeCompiled::class);
    }
});

test('unsupported nodes use the interpreter fallback', function () {
    $environment = EnvironmentFactory::new()->build();
    $template = $environment->parseString('{% assign greeting = "Hello" %}{{ greeting }}');
    $compiledPath = temporaryCompiledTemplatePath();

    try {
        $environment->compile($template, $compiledPath);

        /** @var CompiledTemplateInterface $compiled */
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
        $environment->compile($template, $compiledPath);

        /** @var CompiledTemplateInterface $compiled */
        $compiled = require $compiledPath;

        expect($compiled->render($environment->newRenderContext()))
            ->toBe($template->render($environment->newRenderContext()));
    } finally {
        @unlink($compiledPath);
    }
});

test('compiled literals preserve quotes escapes and control characters', function () {
    $environment = EnvironmentFactory::new()->build();
    $literal = "quote ' and \"\nline\r\t\0 <?php echo 'unsafe'; ?>";
    $template = $environment->parseString($literal);
    $compiledPath = temporaryCompiledTemplatePath();

    try {
        $environment->compile($template, $compiledPath);

        /** @var CompiledTemplateInterface $compiled */
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
    assert($template instanceof Template);
    $template->root->body->pushChild(new CompilableCompilerTestNode('custom output'));
    $compiledPath = temporaryCompiledTemplatePath();

    try {
        $environment->compile($template, $compiledPath);

        /** @var CompiledTemplateInterface $compiled */
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
    assert($template instanceof Template);
    $template->root->body->pushChild(new CompilableCompilerTestTag);
    $compiledPath = temporaryCompiledTemplatePath();

    try {
        $environment->compile($template, $compiledPath);

        /** @var CompiledTemplateInterface $compiled */
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

        /** @var CompiledTemplateInterface $compiled */
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

        /** @var CompiledTemplateInterface $compiled */
        $compiled = require $compiledPath;

        expect($compiled->render($environment->newRenderContext()))
            ->toBe($template->render($environment->newRenderContext()))
            ->toBe('filtered runtime');

        $renderDisabled = static function (TemplateInterface $candidate, RenderContext $context): string {
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
    assert($template instanceof Template);
    $template->root->body->pushChild(new FailingCompilableCompilerTestNode);
    $compiledPath = temporaryCompiledTemplatePath();

    try {
        $environment->compile($template, $compiledPath);

        $compiledSource = file_get_contents($compiledPath);

        expect(str_contains($compiledSource ?: '', 'partial output'))->toBeFalse();

        /** @var CompiledTemplateInterface $compiled */
        $compiled = require $compiledPath;

        expect($compiled->render($environment->newRenderContext()))
            ->toBe('prefixfallback output');
    } finally {
        @unlink($compiledPath);
    }
});

test('compilation fails when a fallback node cannot be safely reconstructed', function () {
    $environment = EnvironmentFactory::new()->build();
    $template = $environment->parseString('prefix', name: 'unsafe.liquid');
    $resource = fopen('php://memory', 'r');

    if ($resource === false) {
        throw new RuntimeException('Unable to create a test resource.');
    }

    assert($template instanceof Template);
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
