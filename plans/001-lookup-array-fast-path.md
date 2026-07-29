# Plan 001: Fast-path array scopes in `RenderContext::internalContextLookup()`

> **Executor instructions**: Follow this plan step by step. Run every
> verification command and confirm the expected result before moving to the
> next step. If anything in the "STOP conditions" section occurs, stop and
> report — do not improvise. When done, update the status row for this plan
> in `plans/README.md`.
>
> **Drift check (run first)**: `git diff --stat 4a1bdd6..HEAD -- src/Render/RenderContext.php`
> If that file changed since this plan was written, compare the "Current state"
> excerpt against the live code before proceeding; on a mismatch, treat it as a
> STOP condition.

## Status

- **Priority**: P1
- **Effort**: S
- **Risk**: LOW
- **Depends on**: none
- **Category**: perf
- **Planned at**: commit `4a1bdd6`, 2026-07-29

## Why this matters

`internalContextLookup()` is the innermost function of variable resolution: it
runs **8989 times per render pass** of the theme benchmark suite. Today every
one of those calls enters a `try`/`catch` block and walks a `match (true)`
chain whose *first* arm tests `$scope instanceof Drop` — even though the
overwhelming majority of scopes are plain PHP arrays (the render scopes, the
context data, and the static variables are all arrays). Hoisting the array case
out of the `match` and above the `try` measured **−12.5% on render and −11.3%
on stream** — the single largest win found in the render phase, from one hunk.

Semantics do not change: the array arm produces exactly the same value, and the
"key absent" case produces the same `MissingValue` the `default` arm produced.

## Current state

- `src/Render/RenderContext.php` — the render-time variable context. The method
  to change is `internalContextLookup()` at lines 221–236.

Exact code as it exists today at `src/Render/RenderContext.php:221-236`:

```php
    public function internalContextLookup(mixed $scope, int|string $key): mixed
    {
        try {
            $value = match (true) {
                $scope instanceof Drop => $scope->{$key},
                is_array($scope) && array_key_exists($key, $scope) => $scope[$key],
                is_object($scope) && $this->objectHasProperty($scope, (string) $key) => $scope->{$key},
                is_object($scope) && $this->objectHasStaticProperty($scope, (string) $key) => $scope::$$key,
                default => $this->missingValue,
            };
        } catch (UndefinedDropMethodException) {
            return $this->missingValue;
        }

        return $this->normalizeValue($value);
    }
```

Facts you need about the surrounding code:

- `$this->missingValue` is a private, pre-allocated `MissingValue` instance
  (declared at `src/Render/RenderContext.php:65`, constructed at line 102).
  Callers detect "not found" with `$value instanceof MissingValue`.
- `normalizeValue()` (same file) resolves `Closure` and `MapsToLiquid` values
  and must still be applied to whatever the array branch returns.
- An array scope can never throw `UndefinedDropMethodException` — that exception
  comes from `Drop::__get()` (`src/Drop.php`) and from `objectHasProperty()`
  reaching a magic getter. So moving the array case outside the `try` is safe.

Repo conventions: PHP 8.2+, strict PHPStan level 9 over `src/`, formatting by
Laravel Pint (`pint.json`). Match the existing style in this file — early
returns and guard clauses are already used throughout (see `normalizeValue()`
and `findVariables()` in the same file).

## Commands you will need

| Purpose | Command | Expected on success |
|---------|---------|---------------------|
| Tests | `vendor/bin/pest` | `Tests: 800 passed` (or more), 0 failures |
| Static analysis | `vendor/bin/phpstan analyse --no-progress` | `[OK] No errors` |
| Formatting | `vendor/bin/pint --test` | exit 0 (no files need fixing) |
| Benchmark | see "Benchmark procedure" below | render/stream both improve |

## Benchmark procedure

Run this from the repo root, after the code change and before committing:

```bash
vendor/bin/phpbench run \
  --filter='benchRender|benchStream' \
  --progress=none \
  --warmup=1 \
  --retry-threshold=5 \
  --report=aggregate \
  --output=json > build/plan-001.json

php tools/phpbench-compare.php build/base.json build/plan-001.json
```

`build/base.json` is the committed baseline for this repo (captured at
`4a1bdd6`). The compare tool only reports on benchmark names present in both
files, so filtering to two subjects is expected and fine.

**Expected**: both `LiquidBench::benchRender` and `LiquidBench::benchStream`
show a positive ops/s delta, roughly **+10% to +14%** each. Benchmark noise on
a developer laptop is ±2–3%; if you see a delta smaller than +5%, re-run once
before drawing a conclusion. A *negative* delta on either subject is a STOP
condition.

Do not commit `build/plan-001.json` — `build/` output other than the committed
`base.json` is scratch. Check `git status` before committing.

## Scope

**In scope** (the only files you should modify):
- `src/Render/RenderContext.php`
- `plans/README.md` (status row only)

**Out of scope** (do NOT touch, even though they look related):
- `src/Render/RenderContext.php`'s `objectHasProperty()` and
  `objectHasStaticProperty()` — they are cold in this benchmark (0 calls) and
  changing them adds risk for no measured gain.
- `normalizeValue()` — an early return for non-objects was measured at exactly
  zero delta and was deliberately rejected. Do not add it.
- `src/Drop.php`, `src/Nodes/VariableLookup.php`, `src/Nodes/BodyNode.php` —
  covered by other plans or explicitly rejected.
- `build/base.json` — the committed baseline. Never regenerate or overwrite it.

## Git workflow

- Work on branch `perf/render-stream-optimizations`. Create it from `main` if it
  does not exist yet: `git switch -c perf/render-stream-optimizations`. If it
  already exists (a previous plan created it), just switch to it.
- One commit for this plan. Message style in this repo is plain sentence case,
  no conventional-commit prefixes (see `git log --oneline`: "Optimize parser and
  renderer hot paths", "Skip partial ParseContext on cache hit, tidy tag
  parsing"). Use: `Fast-path array scopes in context lookup`.
- Do NOT push and do NOT open a PR.

## Steps

### Step 1: Hoist the array case above the try block

In `src/Render/RenderContext.php`, replace the body of
`internalContextLookup()` so that arrays are handled first, and the `match`
keeps only the remaining arms:

```php
    public function internalContextLookup(mixed $scope, int|string $key): mixed
    {
        if (is_array($scope)) {
            if (! array_key_exists($key, $scope)) {
                return $this->missingValue;
            }

            return $this->normalizeValue($scope[$key]);
        }

        try {
            $value = match (true) {
                $scope instanceof Drop => $scope->{$key},
                is_object($scope) && $this->objectHasProperty($scope, (string) $key) => $scope->{$key},
                is_object($scope) && $this->objectHasStaticProperty($scope, (string) $key) => $scope::$$key,
                default => $this->missingValue,
            };
        } catch (UndefinedDropMethodException) {
            return $this->missingValue;
        }

        return $this->normalizeValue($value);
    }
```

Note the removed `is_array($scope) && array_key_exists($key, $scope)` arm — it
is now unreachable and must be deleted, not left in place.

**Verify**: `vendor/bin/pest` → `Tests: 800 passed` (or more), 0 failures.

### Step 2: Confirm static analysis and formatting

**Verify**:
- `vendor/bin/phpstan analyse --no-progress` → `[OK] No errors`
- `vendor/bin/pint --test` → exit 0

If Pint reports the file needs formatting, run `vendor/bin/pint src/Render/RenderContext.php`
and re-run the tests.

### Step 3: Benchmark

Run the "Benchmark procedure" above.

**Verify**: `php tools/phpbench-compare.php build/base.json build/plan-001.json`
→ positive ops/s delta on both `LiquidBench::benchRender` and
`LiquidBench::benchStream`, expected around +10% to +14%.

Record the two delta percentages — you will report them back.

### Step 4: Commit

```bash
git status                    # confirm only src/Render/RenderContext.php and plans/README.md changed
git add src/Render/RenderContext.php plans/README.md
git commit -m "Fast-path array scopes in context lookup"
```

**Verify**: `git show --stat HEAD` → exactly two files changed; `git status` →
clean except untracked `build/plan-001.json`.

## Test plan

No new tests are required: this is a behaviour-preserving refactor of an
internal method that is already covered end to end. The existing coverage that
exercises this path:

- `tests/Integration/ContextTest.php` — scope resolution, nested scopes,
  static data and registers.
- `tests/Integration/VariableTest.php` and `tests/Integration/DropTest.php` —
  array scopes, drop scopes, and missing-variable behaviour.
- `tests/Integration/SelfDropTest.php` — the `self` fallback path, which
  depends on `MissingValue` being returned for absent keys.

If any of these fail, the array fast path is not behaviour-preserving —
that is a STOP condition, not something to patch around.

**Verification**: `vendor/bin/pest` → all pass, same count as before the change.

## Done criteria

ALL must hold:

- [ ] `vendor/bin/pest` exits 0 with the same test count as before the change
- [ ] `vendor/bin/phpstan analyse --no-progress` reports `[OK] No errors`
- [ ] `vendor/bin/pint --test` exits 0
- [ ] `grep -n 'is_array($scope) && array_key_exists' src/Render/RenderContext.php` returns no matches
- [ ] `php tools/phpbench-compare.php build/base.json build/plan-001.json` shows a positive delta on both `benchRender` and `benchStream`
- [ ] `git show --stat HEAD` lists only `src/Render/RenderContext.php` and `plans/README.md`
- [ ] `plans/README.md` status row for 001 updated to DONE

## STOP conditions

Stop and report back (do not improvise) if:

- The code at `src/Render/RenderContext.php:221-236` does not match the
  "Current state" excerpt.
- Any test fails after the change. Do not adjust tests to fit — a failure here
  means the fast path changed behaviour, which it must not.
- The benchmark shows a *negative* delta on either subject across two runs.
- You find yourself needing to modify any file outside the in-scope list.
- `build/base.json` does not exist or `tools/phpbench-compare.php` exits
  non-zero for a reason other than a regression threshold.

## Maintenance notes

- The array branch bypasses the `try`/`catch`. That is only correct while array
  access cannot throw `UndefinedDropMethodException`. If a future change makes
  `$scope` an `ArrayAccess` object routed through this branch, the guard must be
  revisited — `is_array()` deliberately excludes `ArrayAccess`, keep it that way.
- The branch must keep calling `normalizeValue()`; dropping it would silently
  break `Closure` and `MapsToLiquid` values stored directly in a scope. A
  reviewer should check for exactly that.
- Deferred out of this plan: the `objectHasProperty()` path still calls
  `get_object_vars()` (allocating an array of every public property) on each
  probe of a plain-object scope. It measured 0 calls in the theme benchmark, so
  it was left alone; it would matter for an application that puts plain PHP
  objects into the render context.
