<?php

use Keepsuit\Liquid\Compiler\CompiledTemplate;
use Keepsuit\Liquid\Compiler\Compiler;
use Keepsuit\Liquid\Compiler\CompilerContext;
use Keepsuit\Liquid\Contracts\CanBeCompiled;
use Keepsuit\Liquid\EnvironmentFactory;
use Keepsuit\Liquid\Nodes\Node;
use Keepsuit\Liquid\ParsedTemplate;
use Keepsuit\Liquid\Render\RenderContext;
use Keepsuit\Liquid\Render\ResourceLimits;
use Keepsuit\Liquid\Tags\ForTag;

class ImportedCompilerOptimizationNode extends Node implements CanBeCompiled
{
    public function __construct(private readonly string $class) {}

    public function render(RenderContext $context): string
    {
        return $this->class;
    }

    public function compile(CompilerContext $context): void
    {
        $context->writeOutput($context->writeClassName($this->class).'::class');
    }
}

class LegacyCompilerOptimizationTemplate extends CompiledTemplate
{
    public function name(): ?string
    {
        return 'legacy-template';
    }

    protected function renderCompiled(RenderContext $context): iterable
    {
        yield from $this->yieldPartial($context, 'p', null, null, []);
        yield from $this->yieldPartialLoop($context, 'p', null, null, []);
    }

    protected function renderCompiledString(RenderContext $context): string
    {
        return $this->renderPartial($context, 'p', null, null, [])
            .$this->renderPartialLoop($context, 'p', null, null, []);
    }

    protected function yieldPartial(RenderContext $context, string $templateName, mixed $variable, ?string $aliasName, array $attributes): Generator
    {
        yield 'stream';
    }

    protected function renderPartial(RenderContext $context, string $templateName, mixed $variable, ?string $aliasName, array $attributes): string
    {
        return 'render';
    }

    protected function yieldPartialLoop(RenderContext $context, string $templateName, mixed $variable, ?string $aliasName, array $attributes): Generator
    {
        yield '-loop';
    }

    protected function renderPartialLoop(RenderContext $context, string $templateName, mixed $variable, ?string $aliasName, array $attributes): string
    {
        return '-loop';
    }
}

class LegacyCompilerOptimizationForTag extends ForTag
{
    public static function collectionSegmentFor(RenderContext $context, mixed $expression, mixed $from, mixed $limit, bool $reversed, string $name): array
    {
        return [];
    }
}

function compilerOptimizationPath(): string
{
    $path = tempnam(sys_get_temp_dir(), 'php-liquid-opt-');
    if ($path === false) {
        throw new RuntimeException('Unable to create a temporary compiler optimization path.');
    }

    return $path;
}

test('compiled streams omit only unreachable literal buffer flushes', function (string $prefix, bool $flushes) {
    $environment = EnvironmentFactory::new()->build();
    $template = $environment->parseString($prefix.'{{ value }}tail');
    $source = (new Compiler)->compile($template);
    $streamSource = strstr($source, 'protected function renderCompiled(');
    $prefixSource = strstr($streamSource, 'try {', before_needle: true);

    expect(str_contains($prefixSource, 'strlen($buffer) >= 4096'))->toBe($flushes);

    $path = compilerOptimizationPath();
    try {
        $environment->compile($template, $path);
        $compiled = require $path;
        foreach (['short', str_repeat('x', 4096)] as $value) {
            $data = ['value' => $value];
            expect(iterator_to_array($compiled->stream($environment->newRenderContext(data: $data))))
                ->toBe(iterator_to_array($template->stream($environment->newRenderContext(data: $data))));
        }
    } finally {
        @unlink($path);
    }
})->with([
    'small prefix' => ['hello', false],
    'below threshold' => [str_repeat('x', 4095), false],
    'threshold' => [str_repeat('x', 4096), true],
    'multibyte threshold' => [str_repeat('é', 2048), true],
]);

test('compiled class imports preserve collisions and global class names', function () {
    $context = new CompilerContext;
    expect($context->writeClassName('DateTime'))->toBe('DateTime');
    expect($context->writeClassName('Example\\RenderContext'))->toBe('\\Example\\RenderContext');
    expect($context->writeClassName('First\\Value'))->toBe('Value');
    expect($context->writeClassName('Second\\Value'))->toBe('\\Second\\Value');
    expect($context->writeClassName('Third\\value'))->toBe('\\Third\\value');
});

test('compiled artifact identity includes imported class names', function () {
    $environment = EnvironmentFactory::new()->build();
    $classes = [];
    foreach (['First\\Value', 'Second\\Value'] as $class) {
        $template = $environment->parseString('', 'same-name');
        assert($template instanceof ParsedTemplate);
        $template->root->body->pushChild(new ImportedCompilerOptimizationNode($class));
        $path = compilerOptimizationPath();
        try {
            $environment->compile($template, $path);
            $compiled = require $path;
            $classes[] = $compiled::class;
            expect($compiled->render($environment->newRenderContext()))->toBe($class);
            expect(implode('', iterator_to_array($compiled->stream($environment->newRenderContext()))))->toBe($class);
        } finally {
            @unlink($path);
        }
    }
    expect($classes[0])->not->toBe($classes[1]);
});

test('compiled capture bodies are shared while retaining capture hooks', function () {
    $environment = EnvironmentFactory::new()->build();
    $template = $environment->parseString('{% capture title %}Hello {{ name }}{% endcapture %}{{ title }}');
    $source = (new Compiler)->compile($template);
    expect(substr_count($source, 'private function renderBody'))->toBe(1);
    expect(substr_count($source, 'withCapture('))->toBe(2);

    $path = compilerOptimizationPath();
    try {
        $environment->compile($template, $path);
        $compiled = require $path;
        foreach ([$template, $compiled] as $candidate) {
            foreach (['render', 'stream'] as $method) {
                $limits = new class extends ResourceLimits
                {
                    public int $captures = 0;

                    public function withCapture(Closure $closure): mixed
                    {
                        $this->captures++;

                        return parent::withCapture($closure).'!';
                    }
                };
                $context = $environment->newRenderContext(data: ['name' => 'World'], resourceLimits: $limits);
                $output = $candidate->$method($context);
                expect($method === 'render' ? $output : implode('', iterator_to_array($output)))->toBe('Hello World!');
                expect($limits->captures)->toBe(1);
                expect($limits->getCumulativeAssignScore())->toBe(11);
            }
        }
    } finally {
        @unlink($path);
    }
});

test('compiled provenance comments remain data and preserve template identity', function () {
    $environment = EnvironmentFactory::new()->build();
    $name = "unsafe ?>\r\n<?php echo 'marker';\0";
    $template = $environment->parseString('{{ values[key] }}', $name);
    $path = compilerOptimizationPath();
    $bufferLevel = ob_get_level();
    try {
        ob_start();
        $environment->compile($template, $path);
        $compiled = require $path;
        expect(ob_get_clean())->toBe('');
        $source = file_get_contents($path);
        expect($source)->toContain('// Template:', "// Lookup: 'key'", 'use Keepsuit\\Liquid\\Nodes\\VariableLookup;');
        expect(preg_match('/[ \t]+$/m', $source))->toBe(0);
        expect($compiled->name())->toBe($name);
        expect($compiled->render($environment->newRenderContext(data: ['key' => 'product', 'values' => ['product' => 'correct']])))->toBe('correct');
    } finally {
        while (ob_get_level() > $bufferLevel) {
            ob_end_clean();
        }
        @unlink($path);
    }
});

test('existing compiled runtime helper overrides retain their signatures', function () {
    $environment = EnvironmentFactory::new()->build();
    $compiled = new LegacyCompilerOptimizationTemplate;
    expect($compiled->render($environment->newRenderContext()))->toBe('render-loop');
    expect(implode('', iterator_to_array($compiled->stream($environment->newRenderContext()))))->toBe('stream-loop');
    expect(LegacyCompilerOptimizationForTag::collectionSegmentFor($environment->newRenderContext(), null, null, null, false, 'loop'))->toBe([]);
});
