<?php

use Keepsuit\Liquid\Compiler\CompilerContext;
use Keepsuit\Liquid\Contracts\CanBeCompiled;
use Keepsuit\Liquid\EnvironmentFactory;
use Keepsuit\Liquid\Nodes\BodyNode;
use Keepsuit\Liquid\Nodes\Document;
use Keepsuit\Liquid\Nodes\Node;
use Keepsuit\Liquid\Nodes\Raw;
use Keepsuit\Liquid\Nodes\Text;
use Keepsuit\Liquid\Nodes\Variable;
use Keepsuit\Liquid\Parse\TagParseContext;
use Keepsuit\Liquid\Render\RenderContext;
use Keepsuit\Liquid\Tag;
use Keepsuit\Liquid\Template;

class CompilableCompilerTestNode extends Node implements CanBeCompiled
{
    public function __construct(private readonly string $value) {}

    public function render(RenderContext $context): string
    {
        return $this->value;
    }

    public function compile(CompilerContext $context): ?string
    {
        return $context->exportValue($this->value);
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

    public function compile(CompilerContext $context): ?string
    {
        return $context->exportValue('tag output');
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

test('environment compiles a template to a requireable artifact', function () {
    $environment = EnvironmentFactory::new()->build();
    $template = $environment->parseString('Hello {{ name }}');
    $compiledPath = temporaryCompiledTemplatePath();

    try {
        $environment->compile($template, $compiledPath);

        expect($compiledPath)->toBeFile();

        /** @var Template $compiled */
        $compiled = require $compiledPath;

        expect($compiled)->toBeInstanceOf(Template::class);
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

test('compiled rendering emits safe core nodes directly', function () {
    $environment = EnvironmentFactory::new()->build();
    $template = $environment->parseString('Hello {{ name | upcase }}!');
    $compiledPath = temporaryCompiledTemplatePath();

    try {
        $environment->compile($template, $compiledPath);

        $compiledSource = file_get_contents($compiledPath);

        expect($compiledSource)->toContain('renderVariable');
        expect(str_contains($compiledSource ?: '', 'unserialize(base64_decode'))->toBeFalse();

        /** @var Template $compiled */
        $compiled = require $compiledPath;

        expect($compiled->render($environment->newRenderContext(data: ['name' => 'World'])))
            ->toBe('Hello WORLD!');
    } finally {
        @unlink($compiledPath);
    }
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

        /** @var Template $compiled */
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

        /** @var Template $compiled */
        $compiled = require $compiledPath;

        expect($compiled->render($environment->newRenderContext()))->toBe($literal);
    } finally {
        @unlink($compiledPath);
    }
});

test('custom compilable nodes opt in through the compiler context', function () {
    $environment = EnvironmentFactory::new()->build();
    $template = $environment->parseString('prefix');
    $template->root->body->pushChild(new CompilableCompilerTestNode('custom output'));
    $compiledPath = temporaryCompiledTemplatePath();

    try {
        $environment->compile($template, $compiledPath);

        /** @var Template $compiled */
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
    $template->root->body->pushChild(new CompilableCompilerTestTag);
    $compiledPath = temporaryCompiledTemplatePath();

    try {
        $environment->compile($template, $compiledPath);

        /** @var Template $compiled */
        $compiled = require $compiledPath;

        expect($compiled->render($environment->newRenderContext()))
            ->toBe('prefixtag output');
    } finally {
        @unlink($compiledPath);
    }
});
