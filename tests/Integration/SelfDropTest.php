<?php

use Keepsuit\Liquid\EnvironmentFactory;

describe('rendering with template backends', function () {
    test('self bracket lookup resolves variable by name', function (bool $compiled) {
        assertTemplateResult('bar', '{{ self[key] }}', staticData: ['key' => 'foo', 'foo' => 'bar'], compiled: $compiled);
    });

    test('self dot lookup accesses variable by property name', function (bool $compiled) {
        assertTemplateResult('bar', '{{ self.foo }}', staticData: ['foo' => 'bar'], compiled: $compiled);
    });

    test('self lookup uses normal scope hierarchy', function (bool $compiled) {
        assertTemplateResult('local', '{% assign foo = "local" %}{{ self.foo }}', compiled: $compiled);
    });

    test('explicit self variable shadows fallback self drop', function (bool $compiled) {
        assertTemplateResult('override', '{% assign self = "override" %}{{ self }}', compiled: $compiled);
        assertTemplateResult('', '{% assign self = "override" %}{{ self.foo }}', compiled: $compiled);
    });

    test('self bracket lookup with variable key', function (bool $compiled) {
        assertTemplateResult('Alice', '{{ self[key] }}', staticData: ['key' => 'name', 'name' => 'Alice'], compiled: $compiled);
    });

    test('self bracket lookup can be composed in nested expression', function (bool $compiled) {
        assertTemplateResult('found', '{{ a[self["b"]] }}', staticData: ['b' => 'x', 'x' => 'found', 'a' => ['found' => 'found', 'x' => 'found']], compiled: $compiled);
    });

    test('self assigned to variable reflects later assignments', function (bool $compiled) {
        assertTemplateResult('late', '{% assign s = self %}{% assign foo = "late" %}{{ s.foo }}', compiled: $compiled);
    });

    test('self passed to partial keeps caller context', function (bool $compiled) {
        assertTemplateResult('caller_value', '{% render "partial", self: self %}', data: ['outer' => 'caller_value'], partials: [
            'partial' => '{{ self.outer }}',
        ], compiled: $compiled);
    });

    test('implicit self in partial is isolated to partial scope', function (bool $compiled) {
        assertTemplateResult('', '{% render "partial" %}', data: ['outer' => 'caller_value'], partials: [
            'partial' => '{{ self.outer }}',
        ], compiled: $compiled);
    });

    test('nested partials preserve each self context independently', function (bool $compiled) {
        // Middle's implicit self sees its own assign; inner gets middle's self and sees middle_var.
        assertTemplateResult('middle_value', '{% render "middle" %}', partials: [
            'middle' => '{% assign middle_var = "middle_value" %}{% render "inner", self: self %}',
            'inner' => '{{ self.middle_var }}',
        ], compiled: $compiled);

        // Root passes its own self to middle; middle's explicit self can see root's local data.
        assertTemplateResult('root_value', '{% render "middle", self: self %}', data: ['root_var' => 'root_value'], partials: [
            'middle' => '{{ self.root_var }}',
        ], compiled: $compiled);
    });

    test('repeated self lookups compare equal', function (bool $compiled) {
        assertTemplateResult('yes', '{% if self == self %}yes{% endif %}', compiled: $compiled);
    });

    test('assigned self drop compares equal to itself', function (bool $compiled) {
        assertTemplateResult('T', '{% assign s = self %}{% if s == s %}T{% else %}F{% endif %}', compiled: $compiled);
    });

    test('two variables from same self context compare equal', function (bool $compiled) {
        assertTemplateResult('yes', '{% assign a = self %}{% assign b = self %}{% if a == b %}yes{% endif %}', compiled: $compiled);
    });

    test('self missing property renders blank under strict variables', function (bool $compiled) {
        $factory = new EnvironmentFactory;
        $factory->setStrictVariables(true);
        assertTemplateResult('', '{{ self.missing_key }}', factory: $factory, compiled: $compiled);
    });

    test('self defined property resolves correctly under strict variables', function (bool $compiled) {
        $factory = new EnvironmentFactory;
        $factory->setStrictVariables(true);
        assertTemplateResult('42', '{{ self.x }}', staticData: ['x' => 42], factory: $factory, compiled: $compiled);
    });

    test('self passed as render param preserves original scope when variable name collides', function (bool $compiled) {
        assertTemplateResult('42|43',
            '{%- assign var = 42 -%}{%- assign s = self -%}{%- render "snippet", other_self: s -%}',
            partials: [
                'snippet' => '{%- assign var = 43 -%}{{- other_self.var }}|{{ self.var -}}',
            ], compiled: $compiled);
    });

    test('self passed to nested renders preserves each level independently', function (bool $compiled) {
        assertTemplateResult('1|2|3',
            '{%- assign a = 1 -%}{%- assign s1 = self -%}{%- render "snippet1", outer: s1 -%}',
            partials: [
                'snippet1' => '{%- assign a = 2 -%}{%- assign s2 = self -%}{%- render "snippet2", outer: outer, middle: s2 -%}',
                'snippet2' => '{%- assign a = 3 -%}{{- outer.a }}|{{ middle.a }}|{{ self.a -}}',
            ], compiled: $compiled);
    });

    test('explicit null self does not trigger self drop fallback', function (bool $compiled) {
        assertTemplateResult('', '{% assign self = nil %}{{ self }}', compiled: $compiled);
        // The assigned nil should be returned, not the SelfDrop
        assertTemplateResult('yes', '{% assign self = nil %}{% if self == nil %}yes{% endif %}', compiled: $compiled);
    });

    test('self dot self renders blank without infinite recursion', function (bool $compiled) {
        assertTemplateResult('', '{{ self.self }}', compiled: $compiled);
    });
})->with('template backends');
