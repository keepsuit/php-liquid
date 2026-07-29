# Plan 004: Drop the dead `evaluate()` call in `VariableLookup` and correct the `$lookups` type

> **Executor instructions**: Follow this plan step by step. Run every
> verification command and confirm the expected result before moving to the
> next step. If anything in the "STOP conditions" section occurs, stop and
> report — do not improvise. When done, update the status row for this plan
> in `plans/README.md`.
>
> **Drift check (run first)**: `git diff --stat 24a34bb..HEAD -- src/Nodes/VariableLookup.php`
> If that file changed since this plan was written, compare the "Current state"
> excerpts against the live code before proceeding; on a mismatch, treat it as a
> STOP condition.

## Status

- **Priority**: P2
- **Effort**: S
- **Risk**: LOW
- **Depends on**: plans/002-lazy-variable-lookup.md (already DONE — this plan edits code that plan introduced)
- **Category**: perf + bug
- **Planned at**: commit `24a34bb`, 2026-07-29

## Why this matters

Two things, both in `src/Nodes/VariableLookup.php`, discovered together because
one hid the other.

**The performance half.** `RenderContext::evaluate()` is the highest-volume
function in the render path — 11145 calls per pass of the theme benchmark, of
which 9283 (83%) do nothing but one `instanceof` check and a return.
`evaluate($this->name)` accounts for 1862 of those, and it can *never* do
anything: `VariableLookup::$name` is declared `public readonly string`, a real
PHP type declaration the engine enforces, and a string is never
`CanBeEvaluated`. There is even an `assert(is_string($name))` on the next line
confirming the intent. Deleting the call measured **−3.2% render, −2.6% stream**
over three interleaved A/B rounds.

**The correctness half.** `$lookups` is annotated `/** @var string[] */`, and
that is **wrong**. `ExpressionParser::parseVariableLookups()`
(`src/Parse/ExpressionParser.php:64-87`) pushes a plain string for a dot lookup
(`a.b`) but pushes `$this->tokenStream->expression()` for a bracket lookup
(`a[b]`) — which can be a `VariableLookup`, `RangeLookup`, `Literal`, int,
float, bool, or null. PHPStan has been reasoning from a false premise about this
property, and it hides a reachable crash:

```
{{ a[empty] }}  with strictVariables: true
  → InternalException  (wrapping "Object of class Literal could not be converted to string")
```

instead of the `UndefinedVariableException: Variable 'a.empty' not found` that
`{{ a[b] }}` and `{{ a[(1..2)] }}` correctly produce. The cause is
`toString()` calling `implode()` over `$this->lookups`; `Literal` is a backed
enum with no `__toString()`, so the implode throws. `VariableLookup` and
`RangeLookup` happen to survive only because they define `__toString()`.

Fixing the annotation makes PHPStan surface exactly two real problems, both of
which this plan fixes properly.

## Current state

Everything changes in one file: `src/Nodes/VariableLookup.php`.

The class header and imports today (`src/Nodes/VariableLookup.php:1-16`):

```php
<?php

namespace Keepsuit\Liquid\Nodes;

use Keepsuit\Liquid\Contracts\CanBeEvaluated;
use Keepsuit\Liquid\Contracts\HasParseTreeVisitorChildren;
use Keepsuit\Liquid\Contracts\IsContextAware;
use Keepsuit\Liquid\Exceptions\SyntaxException;
use Keepsuit\Liquid\Render\RenderContext;
use Keepsuit\Liquid\Support\MissingValue;
use Keepsuit\Liquid\Support\UndefinedVariable;

class VariableLookup implements CanBeEvaluated, HasParseTreeVisitorChildren
{
    const FILTER_METHODS = ['size', 'first', 'last'];
```

The constructor (`src/Nodes/VariableLookup.php:23-27`):

```php
    public function __construct(
        public readonly string $name,
        /** @var string[] */
        public readonly array $lookups = [],
    ) {
```

`toString()` (`src/Nodes/VariableLookup.php:70-77`):

```php
    public function toString(): string
    {
        if ($this->lookups === []) {
            return $this->name;
        }

        return implode('.', [$this->name, ...$this->lookups]);
    }
```

The head of `evaluate()` (`src/Nodes/VariableLookup.php:89-93`):

```php
    public function evaluate(RenderContext $context): mixed
    {
        $name = $context->evaluate($this->name);
        assert(is_string($name));
        $variables = $context->iterateVariables($name);
```

The filter-fallback branch inside the lookup loop
(`src/Nodes/VariableLookup.php:110-119`):

```php
            foreach ($this->lookups as $i => $lookup) {
                $key = $context->evaluate($lookup) ?? '';

                assert(is_string($key) || is_int($key));

                $nextObject = $context->evaluate($context->internalContextLookup($object, $key));

                if ($nextObject instanceof MissingValue && is_iterable($object) && in_array($i, $this->lookupFilters, true)) {
                    $nextObject = $context->applyFilter($lookup, $object);
                }
```

### Facts you must not get wrong

- **`$context->evaluate($lookup)` on line 111 is NOT dead and must stay.**
  It is what resolves the variable inside a bracket lookup like `{{ a[b] }}`.
  Removing it breaks 7 tests. Only the `evaluate($this->name)` call on line 91
  is dead.
- `$lookupFilters` (built in the constructor) holds only the indices `$i` where
  the lookup is one of the strings in `FILTER_METHODS`. So inside the
  `in_array($i, $this->lookupFilters, true)` branch, `$lookup` is always a
  string at runtime — PHPStan just cannot see it. Narrow it with an explicit
  `is_string($lookup)` in the condition, which is honest rather than a
  suppression.
- The `Expression` type alias is declared on `ExpressionParser`
  (`src/Parse/ExpressionParser.php:11`) as:
  `string|int|float|bool|Literal|VariableLookup|RangeLookup|null`.
  Other files in this repo import it with
  `@phpstan-import-type Expression from ExpressionParser` — see the class
  docblock of `src/Nodes/Variable.php` and `src/Tags/ForTag.php` for the exact
  convention to copy.
- `Literal` (`src/Nodes/Literal.php`) is a **backed enum** (`case Empty = 'empty';`
  `case Blank = 'blank';`), so its string form is `$literal->value`.
- `toString()` is on the cold path only — it is called from the two
  `strictVariables` error branches and from `__toString()`. Readability beats
  micro-optimization there.

### Conventions

PHP 8.2+, PHPStan level 9 over `src/`, Laravel Pint. This repo's CI forbids
silencing analysis errors: **do not add `@phpstan-ignore` comments, baseline
entries, inline `@var` overrides, or type casts to make an error go away.** Fix
the underlying cause. `match (true)` chains are the idiom used across this
codebase for this kind of dispatch — see `Variable::debugLabel()` in
`src/Nodes/Variable.php:136-146` for a near-identical shape you should mirror.

## Commands you will need

| Purpose | Command | Expected on success |
|---------|---------|---------------------|
| Tests | `vendor/bin/pest` | `Tests: 806 passed` (or more), 0 failures |
| Static analysis | `vendor/bin/phpstan analyse --no-progress` | `[OK] No errors` |
| Formatting | `vendor/bin/pint --test` | exit 0 |
| Benchmark | see "Benchmark procedure" | render/stream both improve |

## Benchmark procedure

```bash
vendor/bin/phpbench run \
  --filter='benchRender|benchStream' \
  --progress=none \
  --warmup=1 \
  --retry-threshold=5 \
  --report=aggregate \
  --output=json > build/plan-004.json

php tools/phpbench-compare.php build/base.json build/plan-004.json
```

**Important expectation-setting**: `build/base.json` was captured on CI
hardware, so the absolute percentages it produces locally are inflated and not
meaningful. What matters here is only that neither subject *regresses*. This
plan's own contribution measured **−3.2% render / −2.6% stream** in a controlled
same-machine A/B, which is close to the ±2% band the compare tool treats as
noise — so a local single-run comparison may well show this plan as neutral.
**That is an acceptable outcome and is NOT a STOP condition.** Only a clear
regression (worse than −5% on either subject, reproduced across two runs) is.

Do not commit anything under `build/` other than the already-committed
`base.json`.

## Scope

**In scope** (the only files you should modify):
- `src/Nodes/VariableLookup.php`
- `tests/Integration/VariableTest.php` (add the regression test — see "Test plan")
- `plans/README.md` (status row only)

**Out of scope** (do NOT touch, even though they look related):
- `$context->evaluate($lookup)` on line 111 — see "Facts you must not get
  wrong". It is load-bearing.
- `$context->evaluate($object)` at the top of the `foreach ($variables ...)`
  loop — scope values genuinely can be `CanBeEvaluated`.
- `src/Parse/ExpressionParser.php` — the parser is correct; it was the
  annotation that was wrong.
- `src/Nodes/Variable.php`, `src/Nodes/RangeLookup.php`, `src/Nodes/Literal.php`.
- `src/Render/RenderContext.php::evaluate()` — the other ~5800 no-op calls come
  from call sites whose types do not prove them dead. Not this plan.
- `phpstan-baseline.neon` — must not gain entries.
- `build/base.json`.

## Git workflow

- Work on the existing branch `perf/render-stream-optimizations`. Do NOT create
  a branch and do NOT switch branches.
- One commit for this plan. Repo message style is plain sentence case, no
  conventional-commit prefixes (see `git log --oneline`). Use:
  `Fix variable lookup types and drop a dead evaluate call`
- Do NOT push and do NOT open a PR. A PR (#69) already exists for this branch;
  pushing is the maintainer's call.

## Steps

### Step 1: Reproduce the bug first

Confirm the defect exists before fixing it. Create a scratch file
`/tmp/repro-004.php` (outside the repo — do NOT add it to the repo):

```php
<?php
require '/Users/fabio/projects/packages/php-liquid/vendor/autoload.php';

use Keepsuit\Liquid\EnvironmentFactory;
use Keepsuit\Liquid\Render\RenderContext;
use Keepsuit\Liquid\Render\RenderContextOptions;

$env = EnvironmentFactory::new()->build();
$tpl = $env->parseString('{{ a[empty] }}');
$ctx = new RenderContext(
    options: new RenderContextOptions(strictVariables: true, rethrowErrors: true),
    environment: $env,
);

try {
    $tpl->render($ctx);
    echo "NO EXCEPTION\n";
} catch (\Throwable $e) {
    echo get_class($e).': '.$e->getMessage()."\n";
}
```

**Verify**: `php /tmp/repro-004.php` → prints
`Keepsuit\Liquid\Exceptions\InternalException: Internal exception`.

If it instead prints an `UndefinedVariableException`, the bug is already fixed —
STOP and report.

### Step 2: Delete the dead `evaluate()` call

In `evaluate()`, replace:

```php
        $name = $context->evaluate($this->name);
        assert(is_string($name));
        $variables = $context->iterateVariables($name);
```

with:

```php
        $variables = $context->iterateVariables($this->name);
```

`$name` has no other use in the method — confirm that with
`grep -n '\$name' src/Nodes/VariableLookup.php` before deleting (the only other
hits should be `$this->name`).

**Verify**: `vendor/bin/pest` → `Tests: 806 passed`, 0 failures.

### Step 3: Correct the `$lookups` annotation

Add the type import to the class docblock, immediately above
`class VariableLookup`:

```php
/**
 * @phpstan-import-type Expression from ExpressionParser
 */
class VariableLookup implements CanBeEvaluated, HasParseTreeVisitorChildren
```

and add the matching import alongside the existing `use` statements:

```php
use Keepsuit\Liquid\Parse\ExpressionParser;
```

Then change the constructor annotation:

```php
    public function __construct(
        public readonly string $name,
        /** @var array<Expression> */
        public readonly array $lookups = [],
    ) {
```

**Verify**: `vendor/bin/phpstan analyse --no-progress` → **exactly 2 errors**,
both in `src/Nodes/VariableLookup.php`:
- one on the `implode` in `toString()` (`argument.type`)
- one on the `applyFilter` call in the lookup loop (`argument.type`)

Those two are the real problems the wrong annotation was hiding; steps 4 and 5
fix them. If you see a different number of errors, or errors in other files,
STOP and report.

### Step 4: Make `toString()` handle every lookup type

Replace `toString()` with:

```php
    public function toString(): string
    {
        if ($this->lookups === []) {
            return $this->name;
        }

        $lookups = array_map(
            fn (mixed $lookup): string => match (true) {
                is_string($lookup) => $lookup,
                $lookup instanceof Literal => $lookup->value,
                $lookup instanceof VariableLookup, $lookup instanceof RangeLookup => $lookup->toString(),
                is_bool($lookup) => $lookup ? 'true' : 'false',
                $lookup === null => '',
                default => (string) $lookup,
            },
            $this->lookups,
        );

        return implode('.', [$this->name, ...$lookups]);
    }
```

`default` covers the remaining `int|float`. This mirrors the existing
`Variable::debugLabel()` idiom.

**Verify**: `php /tmp/repro-004.php` → now prints
`Keepsuit\Liquid\Exceptions\UndefinedVariableException: Variable 'a.empty' not found`
(exact quoting of the variable name may differ — what matters is that it is an
`UndefinedVariableException` naming `a.empty`, not an `InternalException`).

### Step 5: Narrow `$lookup` at the filter-fallback call

In the lookup loop, change the condition:

```php
                if ($nextObject instanceof MissingValue && is_string($lookup) && is_iterable($object) && in_array($i, $this->lookupFilters, true)) {
                    $nextObject = $context->applyFilter($lookup, $object);
                }
```

Keep `$nextObject instanceof MissingValue` as the first operand — it is the
cheapest and rarest test, and this is on the hot path.

**Verify**:
- `vendor/bin/phpstan analyse --no-progress` → `[OK] No errors`
- `vendor/bin/pint --test` → exit 0
- `vendor/bin/pest` → `Tests: 806 passed`, 0 failures

### Step 6: Add the regression test

See "Test plan".

**Verify**: `vendor/bin/pest` → all pass, one more test than before.

### Step 7: Benchmark and commit

Run the "Benchmark procedure". Record both deltas, and remember that neutral is
an acceptable result here.

```bash
git status
git add src/Nodes/VariableLookup.php tests/Integration/VariableTest.php plans/README.md
git commit -m "Fix variable lookup types and drop a dead evaluate call"
```

**Verify**: `git show --stat HEAD` → exactly three files.

## Test plan

Add one regression test to `tests/Integration/VariableTest.php`, pinning the bug
from step 1. Read two neighbouring tests in that file first and match their
structure and helper usage (the file uses Pest `test('...', function () { ... })`
with this repo's template helpers).

The case: **a bracket lookup whose key is a `Literal`, under `strictVariables`,
reports an undefined variable rather than an internal error.** Concretely,
rendering `{{ a[empty] }}` with `strictVariables: true` must raise
`UndefinedVariableException`, not `InternalException`.

If `tests/Integration/VariableTest.php` turns out not to be the natural home
(for example if bracket-lookup tests live in `tests/Integration/OutputTest.php`
or `tests/Integration/ContextTest.php`), put it wherever the existing
bracket-lookup tests are and say so in your report — matching the file's
neighbours matters more than the exact file named here.

Do not add a test for the performance change; it is behaviour-preserving and
already covered.

**Verification**: `vendor/bin/pest` → 807 passed (or more), 0 failures.

## Done criteria

ALL must hold:

- [ ] `vendor/bin/pest` exits 0 with one more test than before (807+)
- [ ] `vendor/bin/phpstan analyse --no-progress` reports `[OK] No errors`
- [ ] `vendor/bin/pint --test` exits 0
- [ ] `git diff HEAD~1 -- phpstan-baseline.neon` is empty (no new baseline entries)
- [ ] `grep -n 'assert(is_string($name))' src/Nodes/VariableLookup.php` returns no matches
- [ ] `grep -n '@var string\[\]' src/Nodes/VariableLookup.php` returns no matches
- [ ] `grep -n 'evaluate($lookup)' src/Nodes/VariableLookup.php` still returns a match (the load-bearing call was kept)
- [ ] `php /tmp/repro-004.php` prints an `UndefinedVariableException`, not an `InternalException`
- [ ] `php tools/phpbench-compare.php build/base.json build/plan-004.json` shows no clear regression on either subject
- [ ] `git show --stat HEAD` lists only the three in-scope files
- [ ] `plans/README.md` status row for 004 updated to DONE

## STOP conditions

Stop and report back (do not improvise) if:

- The repro in step 1 does not reproduce.
- After step 3, PHPStan reports anything other than exactly the two expected
  `argument.type` errors in `src/Nodes/VariableLookup.php`.
- You are tempted to add a `@phpstan-ignore` comment, a baseline entry, an
  inline `@var`, or a cast to clear an analysis error. The repo forbids it —
  stop instead.
- Removing the `evaluate($this->name)` call causes any test to fail. It should
  not; if it does, the assumption that `$name` is always a `string` is false and
  that changes the whole plan.
- You conclude that `$context->evaluate($lookup)` on line 111 should also be
  removed. It must not be — that path is what 7 tests cover.
- The benchmark shows worse than −5% on either subject across two runs.

## Maintenance notes

- The `@var array<Expression>` annotation is now the accurate contract. If
  anyone "simplifies" it back to `string[]`, both bugs return silently and
  PHPStan will again reason from a false premise. Worth a comment in review.
- `toString()` is cold (error paths and `__toString()` only), so the `array_map`
  there is not a performance concern. Do not micro-optimize it back into an
  `implode` over raw lookups.
- The `is_string($lookup)` narrowing added in step 5 is redundant at runtime
  (`$lookupFilters` already guarantees it) but is the honest way to express the
  invariant to the analyser. If `$lookupFilters` construction ever changes, that
  guard is what keeps the call safe.
- Deferred: roughly 5800 further no-op `RenderContext::evaluate()` calls per
  render pass remain, from filter arguments, conditions, and `ForTag`. Their
  argument types do not prove them dead, so they need measurement rather than
  deletion. Not attempted here.
