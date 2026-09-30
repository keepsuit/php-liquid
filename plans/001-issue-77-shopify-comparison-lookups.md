# Plan 001: Match Shopify comparisons and lookups while preserving strict case semantics

> **Executor instructions**: Follow the steps in order and verify each one
> before continuing. Stop on any condition in “STOP conditions”; do not invent
> a compatibility mode. When complete, update this plan and its row in
> `plans/README.md`, unless a reviewer has taken responsibility for the index.
>
> **Drift check (run first)**:
> `git diff --stat f31c275..HEAD -- src/Condition/ConditionOperator.php src/Nodes/VariableLookup.php src/Filters/StandardFilters.php src/Tags/CaseTag.php tests/Unit/ConditionTest.php tests/Unit/VariableTest.php tests/Integration/ContextTest.php tests/Integration/Tags/StandardTagTest.php tests/Integration/Tags/IfTagTest.php tests/Integration/StandardFilterTest.php README.md`
> If any in-scope file changed since this plan was written, compare the current
> state below with the live code before proceeding. Also inspect
> `git status --short` for uncommitted edits.

## Status

- **Execution status**: DONE
- **Priority**: P1
- **Effort**: M
- **Risk**: MED
- **Depends on**: none
- **Category**: bug
- **Planned at**: commit `f31c275`, 2026-09-30

## Why this matters

Issue [#77](https://github.com/keepsuit/php-liquid/issues/77) reports behavior
differences found by differential testing against Shopify Liquid 5.13.0. These
differences can make `if`, `unless`, and `case` choose different branches and
make ordinary string and array lookups return empty values. Fixing the listed
examples while preserving the package’s deliberate first-match `case` behavior
improves compatibility without adding a new runtime mode.

## Current state

- `src/Condition/ConditionOperator.php` evaluates equality, ordering, and
  `contains`. At lines 45–59, values with differing PHP types are converted to
  numbers when both are numeric or numeric strings. This makes `5 == '5'` true.
  At lines 62–72, ordering delegates to PHP operators; at lines 113–119, array
  `contains` searches values with `in_array` only.
- `src/Nodes/VariableLookup.php` parses lookup syntax. Its line 17 regex allows
  only non-negative unquoted numeric indexes. At lines 145–148 it falls back
  to filters such as `size`, `first`, and `last` only for iterable values.
- `src/Parse/ExpressionParser.php:66–96` already accepts `arr[-1]` and stores
  the lookup as integer `-1`; the source parser needs no change.
  `VariableLookup::fromMarkup()` is a second path used by
  `RenderContext::get()` and does not yet accept that markup.
- `VariableLookup::walkLookups()` passes negative integer indexes directly to
  `RenderContext::internalContextLookup()`, which treats them as literal PHP
  keys; no list-relative normalization currently happens.
- `src/Filters/StandardFilters.php` already implements `size(string|iterable|null)`
  and uses `Str::length` for strings (lines 410–423), but `first` and `last`
  accept iterables only (lines 226–271).
- `src/Render/RenderContext.php:279–300` resolves array lookups by their key.
  Negative list indexes therefore need to be normalized before this lookup.
- `src/Tags/CaseTag.php` appends `when`/`else` sections in `parse()` (lines
  34–52), returns from `render()` after the first matching section (lines
  55–68), and likewise stops after the first section in `stream()` (lines
  73–85). The parser currently records `else` without checking its position or
  whether another `else` was already recorded (lines 115–161).
- Tests already provide focused patterns: `tests/Unit/ConditionTest.php` covers
  comparison operators, `tests/Unit/VariableTest.php` covers lookup markup,
  `tests/Integration/ContextTest.php` covers rendered property/index lookups,
  and `tests/Integration/Tags/StandardTagTest.php` covers `case` and strict
  syntax errors.
- `README.md:28–31` documents deviations from Shopify but currently only lists
  error modes and the deprecated `include` tag.

### Environment behavior and strictness constraints

`EnvironmentFactory`/`RenderContextOptions` expose:

- `setStrictVariables()` reports undefined variables;
- `setStrictFilters()` reports undefined filters;
- `setRethrowErrors()` controls whether render-time exceptions are rethrown;
- `setLazyParsing()` controls loading/parsing unresolved partials at render time.

The README says parsing is always strict. These options do not choose
comparison semantics or make `case` parsing lax. Do not add an Environment
option for this issue: valid, defined inputs must have the same comparison and
lookup results with default and strict render options. Malformed `case` order
must raise `SyntaxException` while parsing in either configuration.

The existing code and tests favor focused Pest tests next to each behavior and
strict type/value assertions. Keep list containment strict and preserve the
existing positive-index and quoted-key behavior while adding negative indexes.

## Commands you will need

| Purpose | Command | Expected on success |
|---------|---------|---------------------|
| Focused and full tests | `composer test` | Exit 0; all unit and integration tests pass |
| Formatting check | `vendor/bin/pint --test` | Exit 0; no style violations |
| Static analysis | `vendor/bin/phpstan analyse` | Exit 0; no new analysis errors |

Do not use `composer lint` as the formatting gate without checking its effects:
the script runs Pint without check-only mode before PHPStan.

## Scope

**In scope** (only these files should be modified):

- `src/Condition/ConditionOperator.php`
- `src/Nodes/VariableLookup.php`
- `src/Filters/StandardFilters.php`
- `src/Tags/CaseTag.php`
- `tests/Unit/ConditionTest.php`
- `tests/Unit/VariableTest.php`
- `tests/Integration/ContextTest.php`
- `tests/Integration/Tags/StandardTagTest.php`
- `tests/Integration/Tags/IfTagTest.php`
- `tests/Integration/StandardFilterTest.php`
- `README.md`
- `plans/001-issue-77-shopify-comparison-lookups.md` (execution status/checklist)
- `plans/README.md` (plan index status)

**Out of scope**:

- New public Environment or RenderContext options, compatibility modes, or
  error modes.
- Changes to `unless` implementation, generic expression parsing, PHP object or
  Drop member lookup, resource limits, or the `include` tag.
- Changing `case` to render every matching `when` or every `else`. Keep the
  existing first-match behavior.
- Broad changes to all PHP comparison/coercion behavior beyond the issue’s
  stated cases and any additional cases confirmed against Shopify Liquid 5.13.

## Git workflow

- Branch suggestion: `fix/77-shopify-comparison-lookups`, matching the
  repository’s `fix/...` branch naming examples.
- Do not push or open a PR as part of executing this plan. Use the repository’s
  normal commit process only if separately requested.

## Steps

### Step 1: Correct comparison and `contains` rules

In `ConditionOperator`, distinguish numeric PHP values (`int`/`float`) from
numeric strings before the current mixed-type numeric coercion. Make equal
integer/float values compare equal, while keeping a number and string unequal
even when the string contains the same digits. Keep `!=` as the inverse of
equality. For string ordering, compare two strings lexically, including numeric
strings such as `'10'` and `'9'`; do not let PHP reinterpret them numerically.
Preserve numeric ordering for numeric values. Do not guess new cross-type
ordering rules beyond the issue’s cases; check any proposed change against
Shopify Liquid 5.13 first.

For array `contains`, distinguish list-shaped PHP arrays from hash-shaped
arrays: retain strict value membership for lists and check keys for hashes.
Keep null and unsupported left operands false as they are today. Add unit cases
to `tests/Unit/ConditionTest.php` for the issue’s examples and inverse/negative
cases: `5 == 5.0`, `1 == 1.0`, `5 == '5'`, `'10' < '9'`, list value membership,
and associative key membership (`['a' => 1] contains 'a'`).

**Verify**: `composer test -- tests/Unit/ConditionTest.php` → all tests in that
file pass, including the new regression cases.

### Step 2: Support string properties and negative list indexes

Keep `ExpressionParser::parseVariableLookups()` unchanged: it already accepts
`arr[-1]` as an integer lookup, and malformed signed forms already fail strict
parsing. Extend `VariableLookup::fromMarkup()` to accept an unquoted negative
integer and represent it as an integer while keeping quoted `arr["-1"]` a
string key. Normalize a negative integer lookup relative to the end of a
list-shaped array in `VariableLookup::walkLookups()` before calling
`RenderContext::internalContextLookup`; this also handles a dynamic lookup
whose value evaluates to a negative integer. Preserve existing positive
numeric lookup behavior. An out-of-range negative index must follow the
existing missing-value behavior, including `strictVariables` handling.

Extend `StandardFilters::first()` and `last()` to accept strings and return the
first/last character using the package’s existing multibyte string helpers
(see `Keepsuit\Liquid\Support\Str`); do not use byte offsets. `size()` already
supports strings, so make lookup dispatch reach it rather than duplicating its
length logic. Add focused parser tests in `tests/Unit/VariableTest.php`, filter
tests in `tests/Integration/StandardFilterTest.php`, and rendered lookup tests
in `tests/Integration/ContextTest.php` for string size/first/last, `arr[-1]`,
negative dynamic indexes, malformed signed indexes, empty/out-of-range lists,
and quoted negative string keys.

Run the valid lookup cases with default Environment options and with
`setStrictVariables(true)`, `setStrictFilters(true)`,
`setRethrowErrors(true)`, and `setLazyParsing(false)`. Use defined variables and
registered standard filters so this checks that these controls do not change
valid lookup results. Include a multibyte string case to confirm character,
not byte, behavior.

**Verify**: `composer test -- tests/Unit/VariableTest.php tests/Integration/ContextTest.php tests/Integration/StandardFilterTest.php`
→ all tests in those files pass, including the new parsing, rendering, and
default/strict Environment-options cases.

### Step 3: Enforce `case` ordering and document the intentional difference

Keep the first-match behavior already implemented by `CaseTag::render()` and
`CaseTag::stream()`. During `CaseTag::parse()`, track whether a `when` and an
`else` have appeared. Reject any `when` after an `else` and reject a second
`else` with `SyntaxException`. Preserve an existing `case` containing only an
`else`; `tests/Unit/ParseTreeVisitorTest.php` depends on that accepted shape.
Keep the valid form with one final `else` unchanged. Exercise both render and
stream paths to ensure only the first matching `when` renders and `else`
renders only when none match.

Add integration regressions in `tests/Integration/Tags/StandardTagTest.php`
using the issue examples: duplicate matching `when` sections render only the
first body; multiple `else` sections and `else` before `when` fail at parse
time. Also cover comparison-driven `case` output for int/float equality and
number/string inequality, and add an `if`/`unless` branch regression in
`tests/Integration/Tags/IfTagTest.php` so all three tags exercise the shared
comparison behavior.

Update `README.md` under “Differences from Shopify Liquid” to state that
php-liquid intentionally uses first-match `case` semantics and strictly rejects
`when` after `else` and multiple `else` sections. Keep the existing note that
parsing is always strict; do not imply that
`setStrictVariables`, `setStrictFilters`, or `setLazyParsing` toggle parser
strictness.

**Verify**: `composer test -- tests/Integration/Tags/StandardTagTest.php tests/Integration/Tags/IfTagTest.php`
→ all tests in those files pass, including parse-time `SyntaxException`
assertions and first-match render/stream cases.

### Step 4: Run the package verification gates

Run the complete Pest suite, the non-mutating Pint check, and PHPStan after the
focused steps are green. Inspect the final diff to confirm only files listed in
Scope changed and that README accurately describes the deliberate `case`
deviation.

**Verify**: `composer test`, `vendor/bin/pint --test`, and
`vendor/bin/phpstan analyse` → all exit 0; the working diff contains only the
in-scope changes.

## Test plan

- Model comparison unit cases after the existing operator datasets in
  `tests/Unit/ConditionTest.php`.
- Model markup parsing cases after the `VariableLookup::fromMarkup` datasets in
  `tests/Unit/VariableTest.php`.
- Model render behavior after the table-driven strict/default tests in
  `tests/Integration/ContextTest.php` and the case tests in
  `tests/Integration/Tags/StandardTagTest.php`.
- Cover all issue acceptance examples: int/float equality; number/string
  inequality; lexical numeric-string ordering; hash key `contains`; string
  size/first/last; negative list index; first-match case; and parse-time errors
  for invalid `else` placement and duplicate `else`.
- Confirm comparison/lookup outcomes are invariant between default and strict
  Environment settings when all inputs are defined and valid.

## Done criteria

- [x] Every comparison and lookup example listed in issue #77 has an integration
      or unit regression test and passes.
- [x] `case` renders only the first matching `when`; a single final `else`
      renders only if no `when` matches.
- [x] A `when` after `else` and duplicate `else` throw `SyntaxException`
      during parsing with default and strict runtime Environment options.
- [x] Default and strict runtime Environment settings produce the same results
      for valid comparison and lookup inputs.
- [x] `composer test`, `vendor/bin/pint --test`, and
      `vendor/bin/phpstan analyse` exit 0.
- [x] `git status --short` shows no modified paths outside Scope and
      `plans/README.md` records the plan status.

## Execution record

- Focused regression suite: 367 passed, 999 assertions.
- Full suite: `composer test` — 884 passed, 2,338 assertions.
- `vendor/bin/pint --test` — passed.
- `vendor/bin/phpstan analyse --no-progress --error-format=table` — passed.
- Added negative list-index handling in `VariableLookup`; `ExpressionParser`
  already accepted `arr[-1]` and required no change. A `case` with only an
  `else` remains valid for compatibility with the existing parser contract.

## STOP conditions

- The cited code no longer matches the current files after the drift check.
- Shopify Liquid 5.13 produces a different result from issue #77 for one of its
  explicit acceptance examples; report the discrepancy instead of inventing
  broader coercion rules.
- Correctly distinguishing a hash from a list requires changing the package’s
  public data representation or an out-of-scope file.
- Negative indexing would make quoted negative string keys unreachable or
  require a breaking public AST change; stop and report the smallest necessary
  scope adjustment.
- Any fix requires changing Environment option contracts or general parser
  behavior outside `CaseTag`.

## Maintenance notes

- Comparisons are shared by `if`, `unless`, and `case`; keep their regression
  coverage tied to `ConditionOperator`, not to three separate implementations.
- Render and stream are separate execution paths for case bodies; keep their
  first-match behavior aligned.
- `size`, `first`, and `last` are exposed through lookup fallback as well as
  filters. Changes to either dispatch or filter input types can affect both
  paths.
- Preserve the documented distinction between strict parsing and the runtime
  Environment options for undefined variables/filters, exception propagation,
  and partial loading.
