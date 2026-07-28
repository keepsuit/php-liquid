<?php

use Keepsuit\Liquid\Environment;
use Keepsuit\Liquid\EnvironmentFactory;
use Keepsuit\Liquid\Exceptions\SyntaxException;
use Keepsuit\Liquid\Parse\Token;
use Keepsuit\Liquid\Performance\Shopify\CommentFormTag;
use Keepsuit\Liquid\Performance\Shopify\CustomFilters;
use Keepsuit\Liquid\Performance\Shopify\PaginateTag;

test('benchmark corpus token streams stay unchanged', function () {
    $actual = [];

    foreach (lexerFixtureSources() as $name => $source) {
        $actual[$name] = lexerFixtureSerializeTokens(
            lexerFixtureEnvironment()->newParseContext()->tokenize($source)->toArray(),
        );
    }

    expect($actual)->toMatchSnapshot();
});

test('lexer-related syntax errors keep their type, message, and line number', function () {
    $actual = [];
    $environment = lexerFixtureEnvironment();

    foreach (lexerFixtureErrorSources() as $name => $source) {
        try {
            $environment->parseString($source);
            $this->fail("Expected {$name} to throw.");
        } catch (SyntaxException $exception) {
            $actual[$name] = [
                'class' => $exception::class,
                'message' => $exception->getMessage(),
                'lineNumber' => $exception->lineNumber,
            ];
        }
    }

    expect($actual)->toMatchSnapshot();
});

function lexerFixtureEnvironment(): Environment
{
    static $environment;

    return $environment ??= EnvironmentFactory::new()
        ->registerTag(CommentFormTag::class)
        ->registerTag(PaginateTag::class)
        ->registerFilters(CustomFilters::class)
        ->build();
}

function lexerFixtureSources(): array
{
    static $sources;

    if ($sources !== null) {
        return $sources;
    }

    $paths = glob(dirname(__DIR__, 2).'/performance/tests/*/*.liquid');

    if ($paths === false) {
        throw new RuntimeException('Could not find lexer fixture sources.');
    }

    sort($paths);

    $sources = [];
    $root = dirname(__DIR__, 2);

    foreach ($paths as $path) {
        $sources[str_replace($root.'/', '', $path)] = file_get_contents($path) ?: '';
    }

    foreach (lexerFixtureInlineSources() as $name => $source) {
        $sources['inline/'.$name.'.liquid'] = $source;
    }

    return $sources;
}

function lexerFixtureInlineSources(): array
{
    return [
        'hyphenated-identifier-with-number' => '{{ a-5 }}',
        'double-dash-boundary' => '{{ a--b }}',
        'trailing-dash-boundary' => '{{ a- }}',
        'question-mark-identifier' => '{{ a-b? }}',
        'negative-number' => '{{ -5 }}',
        'range-literal' => '{{ 1..5 }}',
        'contains-operator' => '{{ contains foo }}',
        'vertical-tab-whitespace' => "{{\vfoo\v}}",
    ];
}

function lexerFixtureErrorSources(): array
{
    return [
        'missing-variable-terminator' => "\n{{\n  hi",
        'missing-tag-terminator' => "\n{% if\n  hi",
        'unexpected-character' => "\n\n{{ % }}",
        'comment-tag-never-closed' => "\n{% comment %}\ncontent",
        'raw-tag-never-closed' => "\n{% raw %}\ncontent",
        'unexpected-end-of-template' => '{{ value | default: "fallback", }}',
    ];
}

function lexerFixtureSerializeTokens(array $tokens): array
{
    return array_map(
        fn (Token $token) => [$token->type->name, $token->data, $token->lineNumber],
        $tokens,
    );
}
