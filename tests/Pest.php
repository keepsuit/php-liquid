<?php

use Keepsuit\Liquid\Compiler\CompiledTemplate;
use Keepsuit\Liquid\Environment;
use Keepsuit\Liquid\EnvironmentFactory;
use Keepsuit\Liquid\Exceptions\SyntaxException;
use Keepsuit\Liquid\Parse\ParseContext;
use Keepsuit\Liquid\Parse\TokenStream;
use Keepsuit\Liquid\ParsedTemplate;
use Keepsuit\Liquid\Template;
use Keepsuit\Liquid\TemplatesCache\CompiledTemplatesCache;
use Keepsuit\Liquid\Tests\Stubs\StubFileSystem;
use PHPUnit\Framework\ExpectationFailedException;
use Spatie\TemporaryDirectory\TemporaryDirectory;

function testTemporaryDirectory(): TemporaryDirectory
{
    // Retain the owners until process shutdown so caches can keep using their paths.
    static $directories = [];

    return $directories[] = TemporaryDirectory::make()->deleteWhenDestroyed();
}

function testCompiledTemplatesCache(): CompiledTemplatesCache
{
    return new CompiledTemplatesCache(testTemporaryDirectory()->path());
}

function testEnvironmentFactory(bool $compiled): EnvironmentFactory
{
    $factory = EnvironmentFactory::new();

    if ($compiled) {
        $factory->setTemplatesCache(testCompiledTemplatesCache());
    }

    return $factory;
}

/** Parse a source for assertions on the parser, AST or parsed cache format. */
function parseSource(string $source, ?Environment $environment = null): ParsedTemplate
{
    return ($environment ?? Environment::default())->parseString($source);
}

/** Parse a source and compile it when the environment uses the compiled test backend. */
function testParseString(Environment $environment, string $source, ?string $name = null): Template
{
    $template = $environment->parseString($source, $name);

    if (! $environment->templatesCache instanceof CompiledTemplatesCache) {
        return $template;
    }

    $path = tempnam(sys_get_temp_dir(), 'liquid-test-root-');
    if ($path === false) {
        throw new RuntimeException('Unable to create a compiled test template.');
    }

    try {
        $environment->compile($template, $path);
        $compiled = require $path;
        assert($compiled instanceof CompiledTemplate);

        return $compiled;
    } finally {
        unlink($path);
    }
}

/**
 * @throws SyntaxException
 */
function parseTemplate(
    string $source,
    ?Environment $environment = null,
    bool $compiled = false,
): Template {
    return testParseString($environment ?? testEnvironmentFactory($compiled)->build(), $source);
}

function buildRenderContext(
    array $data = [],
    array $staticData = [],
    array $registers = [],
    ?Environment $environment = null
) {
    $context = ($environment ?? Environment::default())->newRenderContext(
        data: $data,
        staticData: $staticData,
    );

    foreach ($registers as $key => $value) {
        $context->setRegister($key, $value);
    }

    return $context;
}

/**
 * @throws \Keepsuit\Liquid\Exceptions\LiquidException
 */
function renderTemplate(
    string $template,
    array $data = [],
    array $staticData = [],
    array $registers = [],
    array $partials = [],
    bool $renderErrors = false,
    bool $strictVariables = false,
    EnvironmentFactory $factory = new EnvironmentFactory,
    bool $compiled = false,
): string {
    if ($compiled) {
        $factory->setTemplatesCache(testCompiledTemplatesCache());
    }

    $environment = $factory
        ->setFilesystem(new StubFileSystem(partials: $partials))
        ->setStrictVariables($strictVariables)
        ->setRethrowErrors(! $renderErrors)->build();

    $template = testParseString($environment, $template);

    $context = buildRenderContext(
        data: $data,
        staticData: $staticData,
        registers: $registers,
        environment: $environment,
    );

    return $template->render($context);
}

/**
 * @return Generator<string>
 *
 * @throws SyntaxException
 */
function streamTemplate(
    string $template,
    array $data = [],
    array $staticData = [],
    array $registers = [],
    array $partials = [],
    bool $renderErrors = false,
    bool $strictVariables = false,
    EnvironmentFactory $factory = new EnvironmentFactory,
    bool $compiled = false,
): Generator {
    if ($compiled) {
        $factory->setTemplatesCache(testCompiledTemplatesCache());
    }

    $environment = $factory
        ->setFilesystem(new StubFileSystem(partials: $partials))
        ->setStrictVariables($strictVariables)
        ->setRethrowErrors(! $renderErrors)->build();

    $template = testParseString($environment, $template);

    $context = buildRenderContext(
        data: $data,
        staticData: $staticData,
        registers: $registers,
        environment: $environment,
    );

    return $template->stream($context);
}

function assertTemplateResult(
    string $expected,
    string $template,
    array $data = [],
    array $staticData = [],
    array $registers = [],
    array $partials = [],
    bool $renderErrors = false,
    bool $strictVariables = false,
    EnvironmentFactory $factory = new EnvironmentFactory,
    bool $compiled = false,
): void {
    expect(renderTemplate(
        template: $template,
        data: $data,
        staticData: $staticData,
        registers: $registers,
        partials: $partials,
        renderErrors: $renderErrors,
        strictVariables: $strictVariables,
        factory: $factory,
        compiled: $compiled,
    ))->toBe($expected);
}

function assertMatchSyntaxError(
    string $error,
    string $template,
    array $data = [],
    array $staticData = [],
    array $registers = [],
    array $partials = [],
    bool $compiled = false,
): void {
    try {
        renderTemplate(template: $template, data: $data, staticData: $staticData, registers: $registers, partials: $partials, compiled: $compiled);
    } catch (SyntaxException $exception) {
        expect($exception->toLiquidErrorMessage())->toBe($error);

        return;
    }

    throw new ExpectationFailedException('Syntax Exception not thrown.');
}

function tokenize(string $source): TokenStream
{
    return (new ParseContext)->tokenize($source);
}

function parse(string|TokenStream $source)
{
    return (new ParseContext)->parse($source instanceof TokenStream ? $source : tokenize($source));
}
