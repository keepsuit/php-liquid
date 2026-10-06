<?php

use Keepsuit\Liquid\EnvironmentFactory;
use Keepsuit\Liquid\Exceptions\InternalException;
use Keepsuit\Liquid\Exceptions\InvalidArgumentException;
use Keepsuit\Liquid\Exceptions\UndefinedFilterException;
use Keepsuit\Liquid\Exceptions\UndefinedVariableException;
use Keepsuit\Liquid\Filters\FiltersProvider;
use Keepsuit\Liquid\Tests\Stubs\StubFileSystem;

class StringFilterOverride extends FiltersProvider
{
    public function append(string $input, string $append): string
    {
        return $input.'|'.$append;
    }
}

describe('rendering with template backends', function () {
    test('string filters use Liquid coercion for inputs and arguments', function (bool $compiled, string $source, string $expected) {
        assertTemplateResult($expected, $source, compiled: $compiled);
    })->with([
        ['{{ true | upcase }}', 'TRUE'],
        ["{{ false | append: '!' }}", 'false!'],
        ['{{ true | base64_encode }}', 'dHJ1ZQ=='],
        ["{{ 'abc' | append: nil }}", 'abc'],
        ["{{ 'aaa' | replace: 'a' }}", ''],
        ["{{ 'aaa' | replace_first: 'a' }}", 'aa'],
        ['{{ true | downcase }}', 'true'],
        ['{{ false | capitalize }}', 'False'],
        ['{{ nil | prepend: false }}', 'false'],
        ["{{ 'true' | remove: true }}", ''],
        ["{{ 'true true' | remove_first: true }}", ' true'],
        ["{{ 'true true' | remove_last: true }}", 'true '],
        ["{{ 'true' | replace: true, false }}", 'false'],
        ["{{ 'true true' | replace_last: true, nil }}", 'true '],
        ['{{ true | newline_to_br }}', 'true'],
        ['{{ false | strip }}', 'false'],
        ['{{ true | lstrip }}', 'true'],
        ['{{ false | rstrip }}', 'false'],
        ['{{ true | squish }}', 'true'],
        ['{{ false | strip_newlines }}', 'false'],
        ['{{ true | url_encode }}', 'true'],
        ['{{ false | url_decode }}', 'false'],
        ['{{ true | truncate: 3, false }}', 'false'],
        ["{{ 'abc' | truncate: 1, nil }}", 'a'],
        ["{{ 'one two' | truncatewords: 1, false }}", 'onefalse'],
    ]);

    test('HTML filters escape only HTML characters and remove multiline blocks', function (bool $compiled, string $filter, string $input, string $expected) {
        assertTemplateResult($expected, "{{ value | $filter }}", data: ['value' => $input], compiled: $compiled);
    })->with([
        ['escape', "'é", '&#39;é'],
        ['escape_once', "'é", '&#39;é'],
        ['h', "'é", '&#39;é'],
        ['escape', '&<>"\'é', '&amp;&lt;&gt;&quot;&#39;é'],
        ['escape_once', '&amp; &lt; &#39; &bogus; &#x27; &', '&amp; &lt; &#39; &bogus; &amp;#x27; &amp;'],
        ['escape', '&amp;', '&amp;amp;'],
        ['strip_html', "<script>\nx\n</script>y", 'y'],
        ['strip_html', "<style>\nx</style>y", 'y'],
        ['strip_html', "<!-- a\nb -->y", 'y'],
        ['strip_html', "<p>é</p><script type=\"text/javascript\">\na\n</script><style>\nb\n</style><!--\nc\n-->y", 'éy'],
    ]);

    test('HTML filters coerce booleans and nil', function (bool $compiled, string $filter) {
        assertTemplateResult('true|false|', "{{ true | $filter }}|{{ false | $filter }}|{{ nil | $filter }}", compiled: $compiled);
    })->with(['escape', 'escape_once', 'h', 'strip_html']);

    test('Base64 URL-safe filters match Shopify including padding', function (bool $compiled, string $source, string $expected) {
        assertTemplateResult($expected, $source, compiled: $compiled);
    })->with([
        ["{{ '???' | base64_url_safe_encode }}", 'Pz8_'],
        ["{{ '>>>' | base64_url_safe_encode }}", 'Pj4-'],
        ["{{ 'a' | base64_url_safe_encode }}", 'YQ=='],
        ["{{ 'Pz8_' | base64_url_safe_decode }}", '???'],
        ["{{ 'Pj4-' | base64_url_safe_decode }}", '>>>'],
        ["{{ 'YQ==' | base64_url_safe_decode }}", 'a'],
        ["{{ 'YQ' | base64_url_safe_decode }}", 'a'],
        ["{{ 'YWI' | base64_url_safe_decode }}", 'ab'],
        ["{{ 'Pz8/' | base64_url_safe_decode }}", '???'],
        ['{{ true | base64_url_safe_encode }}', 'dHJ1ZQ=='],
        ['{{ false | base64_url_safe_encode }}', 'ZmFsc2U='],
        ['{{ nil | base64_url_safe_encode }}|{{ nil | base64_url_safe_decode }}', '|'],
    ]);

    test('Base64 decoders reject malformed encodings with a Liquid argument error', function (bool $compiled, string $filter, string $input) {
        expect(fn () => renderTemplate("{{ value | $filter }}", data: ['value' => $input], compiled: $compiled))
            ->toThrow(InvalidArgumentException::class);
    })->with([
        ['base64_decode', 'YQ'],
        ['base64_decode', "YQ==\n"],
        ['base64_decode', 'YR=='],
        ['base64_url_safe_decode', 'Y'],
        ['base64_url_safe_decode', 'YQ='],
        ['base64_url_safe_decode', 'YQ==='],
        ['base64_url_safe_decode', 'YR'],
        ['base64_url_safe_decode', '???'],
    ]);

    test('string coercion keeps environment options independent', function (bool $compiled, bool $strictVariables, bool $strictFilters, bool $rethrowErrors, bool $stream) {
        $environment = testEnvironmentFactory($compiled)
            ->setStrictVariables($strictVariables)
            ->setStrictFilters($strictFilters)
            ->setRethrowErrors($rethrowErrors)
            ->setLazyParsing(false)->build();
        $context = $environment->newRenderContext(data: ['value' => null]);
        $template = testParseString($environment, "{{ true | upcase }}|{{ false | append: value }}|{{ 'aaa' | replace: 'a' }}|{{ true | h }}|{{ '???' | base64_url_safe_encode }}|{{ 'YQ' | base64_url_safe_decode }}");

        expect($stream ? implode('', iterator_to_array($template->stream($context))) : $template->render($context))
            ->toBe('TRUE|false||true|Pz8_|a')
            ->and($context->getErrors())->toBe([]);
    })->with([false, true], [false, true], [false, true], [false, true]);

    test('string filters report missing inputs and arguments under strict variables', function (bool $compiled, string $source, bool $strictVariables, bool $rethrowErrors, bool $stream) {
        $environment = testEnvironmentFactory($compiled)
            ->setStrictVariables($strictVariables)
            ->setRethrowErrors($rethrowErrors)->build();
        $context = $environment->newRenderContext();
        $template = testParseString($environment, $source);
        $render = fn () => $stream ? implode('', iterator_to_array($template->stream($context))) : $template->render($context);

        if ($strictVariables && $rethrowErrors) {
            expect($render)->toThrow(UndefinedVariableException::class, 'Variable `missing` not found');
        } else {
            expect($render())->toBe('');
        }

        expect($context->getErrors())->toHaveCount($strictVariables ? 1 : 0);
        if ($strictVariables) {
            expect($context->getErrors()[0])->toBeInstanceOf(UndefinedVariableException::class);
        }
    })->with([
        '{{ missing | upcase }}',
        '{{ nil | append: missing }}',
        '{{ missing | escape }}',
        '{{ missing | escape_once }}',
        '{{ missing | base64_url_safe_decode }}',
        "{{ 'aaa' | replace: 'a', missing }}",
        '{{ nil | truncate: missing }}',
        '{{ nil | truncatewords: missing }}',
        '{{ nil | truncate: 1, missing }}',
        '{{ nil | truncatewords: 1, missing }}',
    ])->with([false, true], [false, true], [false, true]);

    test('strict variables checks unused string suffix arguments', function (bool $compiled, string $filter) {
        expect(fn () => renderTemplate("{{ 'one' | $filter: 10, missing }}", strictVariables: true, compiled: $compiled))
            ->toThrow(UndefinedVariableException::class);
    })->with(['truncate', 'truncatewords']);

    test('unknown filters still follow strict filters after string coercion', function (bool $compiled, bool $strictFilters, bool $rethrowErrors) {
        $environment = testEnvironmentFactory($compiled)
            ->setStrictFilters($strictFilters)
            ->setRethrowErrors($rethrowErrors)->build();
        $context = $environment->newRenderContext();
        $template = testParseString($environment, '{{ true | upcase | unknown }}');

        if ($strictFilters && $rethrowErrors) {
            expect(fn () => $template->render($context))->toThrow(UndefinedFilterException::class);
        } else {
            expect($template->render($context))->toBe($strictFilters ? '' : 'TRUE');
        }

        expect($context->getErrors())->toHaveCount($strictFilters ? 1 : 0);
    })->with([false, true], [false, true]);

    test('invalid string arguments follow error propagation options', function (bool $compiled, string $source, bool $rethrowErrors, bool $stream) {
        $environment = testEnvironmentFactory($compiled)->setRethrowErrors($rethrowErrors)->build();
        $context = $environment->newRenderContext();
        $template = testParseString($environment, $source);
        $render = fn () => $stream ? implode('', iterator_to_array($template->stream($context))) : $template->render($context);

        if ($rethrowErrors) {
            expect($render)->toThrow(InvalidArgumentException::class);
        } else {
            expect($render())->toStartWith('Liquid error (line 1): ')->not->toContain('Internal exception');
        }

        expect($context->getErrors())->toHaveCount(1)
            ->and($context->getErrors()[0])->toBeInstanceOf(InvalidArgumentException::class);
    })->with([
        "{{ '???' | base64_decode }}",
        "{{ 'YQ=' | base64_url_safe_decode }}",
        "{{ 'abc' | slice: 'invalid' }}",
        "{{ 'abc' | truncate: nil }}",
        "{{ 'one two' | truncatewords: true }}",
    ])->with([false, true], [false, true]);

    test('string coercion works in partials with either lazy parsing setting', function (bool $compiled, bool $lazyParsing, bool $stream) {
        $environment = testEnvironmentFactory($compiled)
            ->setStrictVariables(true)
            ->setStrictFilters(true)
            ->setRethrowErrors(true)
            ->setLazyParsing($lazyParsing)
            ->setFilesystem(new StubFileSystem(['string' => '{{ value | append: nil | h }}']))->build();
        $template = testParseString($environment, "{% render 'string', value: false %}");
        $context = $environment->newRenderContext();

        expect($stream ? implode('', iterator_to_array($template->stream($context))) : $template->render($context))
            ->toBe('false');
    })->with([false, true], [false, true]);

    test('custom string filter overrides retain their parameter types', function (bool $compiled) {
        $factory = EnvironmentFactory::new()->registerFilters(StringFilterOverride::class);

        expect(renderTemplate('{{ true | append: false }}', factory: $factory, compiled: $compiled))->toBe('1|');
        expect(fn () => renderTemplate("{{ nil | append: 'x' }}", factory: $factory, compiled: $compiled))
            ->toThrow(InternalException::class);
    });

    test('required string arguments remain required', function (bool $compiled, string $source) {
        expect(fn () => renderTemplate($source, compiled: $compiled))->toThrow(InternalException::class);
    })->with(["{{ 'abc' | append }}", "{{ 'abc' | split }}", "{{ 'abc' | replace_last: 'a' }}"]);

    test('string replacements handle an empty search like Shopify', function (bool $compiled, string $source, string $expected) {
        assertTemplateResult($expected, $source, compiled: $compiled);
    })->with([
        ["{{ 'hé' | replace: nil, '!' }}", '!h!é!'],
        ["{{ '' | replace: '', '!' }}", '!'],
        ["{{ 'hé' | replace_first: nil, '!' }}", '!hé'],
        ["{{ 'hé' | replace_last: nil, '!' }}", 'hé!'],
        ["{{ 'hé' | remove: nil }}", 'hé'],
        ["{{ 'abc' | truncate: -9223372036854775808 }}", '...'],
    ]);

    test('split slice and truncatewords match Shopify string semantics', function (bool $compiled, string $source, string $expected) {
        assertTemplateResult($expected, $source, data: ['items' => ['a', 'b', 'c']], compiled: $compiled);
    })->with([
        ["{{ '  a  b \n c  ' | split: ' ' | size }}", '3'],
        ["{{ 'a\u{00a0}b c' | split: ' ' | join: '-' }}", "a\u{00a0}b-c"],
        ["{{ 'a,b,,c,,' | split: ',' | size }}", '4'],
        ["{{ ',a,,b,' | split: ',' | join: '-' }}", '-a--b'],
        ["{{ '' | split: ',' | size }}", '0'],
        ["{{ ',' | split: ',' | size }}", '0'],
        ["{{ 'héllo' | split: '' | join: '-' }}", 'h-é-l-l-o'],
        ["{{ 'hé' | split: nil | join: '-' }}", 'h-é'],
        ['{{ true | split: false | join }}', 'true'],
        ["{{ 'abc' | slice: -3 }}", 'a'],
        ["{{ 'héllo' | slice: -5, 2 }}", 'hé'],
        ["{{ 'abc' | slice: -4 }}", ''],
        ["{{ 'abc' | slice: 3 }}", ''],
        ["{{ 'abc' | slice: 0, -1 }}", ''],
        ["{{ 'abc' | slice: 0, nil }}", 'a'],
        ["{{ 'abc' | slice: 0, false }}", 'a'],
        ["{{ 'abc' | slice: '1', '2' }}", 'bc'],
        ['{{ true | slice: 0, 2 }}', 'tr'],
        ['{{ items | slice: -3 | join }}', 'a'],
        ['{{ items | slice: 0, -1 | size }}', '0'],
        ["{{ '  one two three' | truncatewords: 1 }}", 'one...'],
        ["{{ '  one  two  ' | truncatewords: 2 }}", 'one two...'],
        ["{{ 'one ' | truncatewords: 1 }}", 'one...'],
        ["{{ '  one  two  ' | truncatewords: 3 }}", '  one  two  '],
        ["{{ ' \t\n ' | truncatewords: 1 }}", " \t\n "],
        ["{{ 'one two three' | truncatewords: '1' }}", 'one...'],
        ["{{ 'a\u{00a0}b c' | truncatewords: 1 }}", "a\u{00a0}b..."],
        ["{{ 'one two' | truncatewords: 9223372036854775807 }}", 'one two'],
    ]);
})->with('template backends');
