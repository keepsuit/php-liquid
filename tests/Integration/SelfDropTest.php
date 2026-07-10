<?php

use Keepsuit\Liquid\EnvironmentFactory;

// US 1: dynamic bracket lookup
test('self bracket lookup resolves variable by name', function () {
    assertTemplateResult('bar', '{{ self[key] }}', staticData: ['key' => 'foo', 'foo' => 'bar']);
});

// US 2: dot lookup
test('self dot lookup accesses variable by property name', function () {
    assertTemplateResult('bar', '{{ self.foo }}', staticData: ['foo' => 'bar']);
});

// US 3: scope chain — local variable shadows broader
test('self lookup uses normal scope hierarchy', function () {
    assertTemplateResult('local', '{% assign foo = "local" %}{{ self.foo }}');
});

// US 4: explicit self assignment shadows implicit self drop
test('explicit self variable shadows fallback self drop', function () {
    assertTemplateResult('override', '{% assign self = "override" %}{{ self }}');
    assertTemplateResult('', '{% assign self = "override" %}{{ self.foo }}');
});

// US 5: dynamic key that is itself a variable
test('self bracket lookup with variable key', function () {
    assertTemplateResult('Alice', '{{ self[key] }}', staticData: ['key' => 'name', 'name' => 'Alice']);
});

// US 6: nested expression
test('self bracket lookup can be composed in nested expression', function () {
    assertTemplateResult('found', '{{ a[self["b"]] }}', staticData: ['b' => 'x', 'x' => 'found', 'a' => ['found' => 'found', 'x' => 'found']]);
});

// US 7 & 11: assigned self reflects later changes (no snapshot)
test('self assigned to variable reflects later assignments', function () {
    assertTemplateResult('late', '{% assign s = self %}{% assign foo = "late" %}{{ s.foo }}');
});

// US 8: self passed to partial keeps caller context
// data (not staticData) is local to the root context and not accessible in partials normally.
// A passed self retains the root context, so {{ self.outer }} resolves through root's data.
test('self passed to partial keeps caller context', function () {
    assertTemplateResult('caller_value', '{% render "partial", self: self %}', data: ['outer' => 'caller_value'], partials: [
        'partial' => '{{ self.outer }}',
    ]);
});

// US 9: implicit self in partial is isolated to its own scope
// The partial's own implicit self cannot see root's local data.
test('implicit self in partial is isolated to partial scope', function () {
    assertTemplateResult('', '{% render "partial" %}', data: ['outer' => 'caller_value'], partials: [
        'partial' => '{{ self.outer }}',
    ]);
});

// US 10: nested partials preserve each self context independently.
// Middle's implicit self sees middle's own assigns.
// Inner receives middle's implicit self, so it also sees middle's assigns.
// Neither sees root's local data (which is not accessible via implicit self in a partial).
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

// US 12: repeated self lookups compare equal
test('repeated self lookups compare equal', function () {
    assertTemplateResult('yes', '{% if self == self %}yes{% endif %}');
});

// US 13: two variables assigned from same self context compare equal
test('two variables from same self context compare equal', function () {
    assertTemplateResult('yes', '{% assign a = self %}{% assign b = self %}{% if a == b %}yes{% endif %}');
});

// US 14: missing property renders blank under strict variables
test('self missing property renders blank under strict variables', function () {
    $factory = new EnvironmentFactory;
    $factory->setStrictVariables(true);
    assertTemplateResult('', '{{ self.missing_key }}', factory: $factory);
});

// US 15: explicit null self is not confused with missing self
test('explicit null self does not trigger self drop fallback', function () {
    assertTemplateResult('', '{% assign self = nil %}{{ self }}');
    // The assigned nil should be returned, not the SelfDrop
    assertTemplateResult('yes', '{% assign self = nil %}{% if self == nil %}yes{% endif %}');
});

// Regression: self.self does not infinitely recurse and renders blank
test('self dot self renders blank without infinite recursion', function () {
    assertTemplateResult('', '{{ self.self }}');
});
