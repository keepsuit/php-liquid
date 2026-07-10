<?php

use Keepsuit\Liquid\EnvironmentFactory;

test('self bracket lookup resolves variable by name', function () {
    assertTemplateResult('bar', '{{ self[key] }}', staticData: ['key' => 'foo', 'foo' => 'bar']);
});

test('self dot lookup accesses variable by property name', function () {
    assertTemplateResult('bar', '{{ self.foo }}', staticData: ['foo' => 'bar']);
});

test('self lookup uses normal scope hierarchy', function () {
    assertTemplateResult('local', '{% assign foo = "local" %}{{ self.foo }}');
});

test('explicit self variable shadows fallback self drop', function () {
    assertTemplateResult('override', '{% assign self = "override" %}{{ self }}');
    assertTemplateResult('', '{% assign self = "override" %}{{ self.foo }}');
});

test('self bracket lookup with variable key', function () {
    assertTemplateResult('Alice', '{{ self[key] }}', staticData: ['key' => 'name', 'name' => 'Alice']);
});

test('self bracket lookup can be composed in nested expression', function () {
    assertTemplateResult('found', '{{ a[self["b"]] }}', staticData: ['b' => 'x', 'x' => 'found', 'a' => ['found' => 'found', 'x' => 'found']]);
});

test('self assigned to variable reflects later assignments', function () {
    assertTemplateResult('late', '{% assign s = self %}{% assign foo = "late" %}{{ s.foo }}');
});

test('self passed to partial keeps caller context', function () {
    assertTemplateResult('caller_value', '{% render "partial", self: self %}', data: ['outer' => 'caller_value'], partials: [
        'partial' => '{{ self.outer }}',
    ]);
});

test('implicit self in partial is isolated to partial scope', function () {
    assertTemplateResult('', '{% render "partial" %}', data: ['outer' => 'caller_value'], partials: [
        'partial' => '{{ self.outer }}',
    ]);
});

test('nested partials preserve each self context independently', function () {
    // Middle's implicit self sees its own assign; inner gets middle's self and sees middle_var.
    assertTemplateResult('middle_value', '{% render "middle" %}', partials: [
        'middle' => '{% assign middle_var = "middle_value" %}{% render "inner", self: self %}',
        'inner' => '{{ self.middle_var }}',
    ]);

    // Root passes its own self to middle; middle's explicit self can see root's local data.
    assertTemplateResult('root_value', '{% render "middle", self: self %}', data: ['root_var' => 'root_value'], partials: [
        'middle' => '{{ self.root_var }}',
    ]);
});

test('repeated self lookups compare equal', function () {
    assertTemplateResult('yes', '{% if self == self %}yes{% endif %}');
});

test('assigned self drop compares equal to itself', function () {
    assertTemplateResult('T', '{% assign s = self %}{% if s == s %}T{% else %}F{% endif %}');
});

test('two variables from same self context compare equal', function () {
    assertTemplateResult('yes', '{% assign a = self %}{% assign b = self %}{% if a == b %}yes{% endif %}');
});

test('self missing property renders blank under strict variables', function () {
    $factory = new EnvironmentFactory;
    $factory->setStrictVariables(true);
    assertTemplateResult('', '{{ self.missing_key }}', factory: $factory);
});

test('self defined property resolves correctly under strict variables', function () {
    $factory = new EnvironmentFactory;
    $factory->setStrictVariables(true);
    assertTemplateResult('42', '{{ self.x }}', staticData: ['x' => 42], factory: $factory);
});

test('self passed as render param preserves original scope when variable name collides', function () {
    assertTemplateResult('42|43',
        '{%- assign var = 42 -%}{%- assign s = self -%}{%- render "snippet", other_self: s -%}',
        partials: [
            'snippet' => '{%- assign var = 43 -%}{{- other_self.var }}|{{ self.var -}}',
        ]
    );
});

test('self passed to nested renders preserves each level independently', function () {
    assertTemplateResult('1|2|3',
        '{%- assign a = 1 -%}{%- assign s1 = self -%}{%- render "snippet1", outer: s1 -%}',
        partials: [
            'snippet1' => '{%- assign a = 2 -%}{%- assign s2 = self -%}{%- render "snippet2", outer: outer, middle: s2 -%}',
            'snippet2' => '{%- assign a = 3 -%}{{- outer.a }}|{{ middle.a }}|{{ self.a -}}',
        ]
    );
});

test('explicit null self does not trigger self drop fallback', function () {
    assertTemplateResult('', '{% assign self = nil %}{{ self }}');
    // The assigned nil should be returned, not the SelfDrop
    assertTemplateResult('yes', '{% assign self = nil %}{% if self == nil %}yes{% endif %}');
});

test('self dot self renders blank without infinite recursion', function () {
    assertTemplateResult('', '{{ self.self }}');
});
