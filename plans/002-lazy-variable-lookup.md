# Plan 002: Stop scanning every scope — add a lazy `RenderContext::iterateVariables()`

> **Executor instructions**: Follow this plan step by step. Run every
> verification command and confirm the expected result before moving to the
> next step. If anything in the "STOP conditions" section occurs, stop and
> report — do not improvise. When done, update the status row for this plan
> in `plans/README.md`.
>
> **Drift check (run first)**: `git diff --stat 4a1bdd6..HEAD -- src/Render/RenderContext.php src/Nodes/VariableLookup.php`
> Plan 001 (`plans/001-lookup-array-fast-path.md`) also edits
> `src/Render/RenderContext.php`, in `internalContextLookup()`. A diff limited
> to that method is expected. Any other change to the two methods quoted under
> "Current state" is a STOP condition.

## Status

- **Priority**: P1
- **Effort**: S
- **Risk**: LOW
- **Depends on**: plans/001-lookup-array-fast-path.md (ordering only — not a hard dependency)
- **Category**: perf
- **Planned at**: commit `4a1bdd6`, 2026-07-29

## Why this matters

`findVariables()` resolves a bare variable name against every scope. Today it
*always* probes all of them — every render scope, then the context data, then
the static variables — collects every match into an array, and returns it.
Instrumenting one render pass of the theme benchmark: **1862 calls performing
7394 scope probes**, while nearly every caller consumes only the first match.

The extra matches are not dead weight in general: `VariableLookup::evaluate()`
falls back to the next candidate when a *lookup path* (`a.b.c`) misses on the
nearest one. But that fallback is rare, and for a bare `{{ foo }}` — which has
no lookups at all — the later probes can never be used.

Making the scan lazy (a `Generator` that yields matches as it finds them) lets
both callers stop at the first usable value while keeping the fallback semantics
exactly intact. Measured on top of plan 001: **−7.6% render, −6.9% stream**.

`findVariables()` stays in place with its current signature and behaviour, so
this is not a breaking change for anyone calling it from a custom tag or drop.

## Current state

Two files change.

### `src/Render/RenderContext.php` — the scope scanner

Exact code today at `src/Render/RenderContext.php:180-214`:

```php
    public function findVariables(string $key): array
    {
        $variables = [];

        // Check the variable in all scopes + env data + static variables
        $scopeCount = count($this->scopes);
        for ($index = 0; $index < $scopeCount + 2; $index++) {
            $scope = match (true) {
                $index < $scopeCount => $this->scopes[$index],
                $index === $scopeCount => $this->data,
                default => $this->sharedState->staticVariables,
            };

            $value = $this->internalContextLookup($scope, $key);

            if (! $value instanceof MissingValue) {
                $variables[] = $value;
            }
        }

        // Inject the implicit self drop only when no value (including explicit null) was found.
        // An explicit `self = nil` leaves [null] in $variables, so the fallback is skipped,
        // correctly distinguishing defined-null from undefined.
        if ($variables === [] && $key === 'self') {
            return [$this->getSelfDrop()];
        }

        foreach ($variables as $variable) {
            if ($variable instanceof IsContextAware) {
                $variable->setContext($this);
            }
        }

        return $variables;
    }
```

Three behaviours in there are load-bearing and must survive:

1. **Scan order**: innermost scope first (`$this->scopes[0]` … `[n]`), then
   `$this->data`, then `$this->sharedState->staticVariables`.
2. **The `self` fallback**: the implicit `SelfDrop` is injected *only* when
   nothing at all was found. An explicit `self = nil` yields `[null]`, which is
   not empty, so the fallback is correctly skipped. Preserve that distinction —
   track "did we find anything" as a flag; do not test the yielded value for
   `null`.
3. **`setContext()`**: every returned value implementing `IsContextAware` gets
   the context injected before the caller sees it.

### `src/Nodes/VariableLookup.php` — the main consumer

Exact code today at `src/Nodes/VariableLookup.php:89-101` (the head of
`evaluate()`):

```php
    public function evaluate(RenderContext $context): mixed
    {
        $name = $context->evaluate($this->name);
        assert(is_string($name));
        $variables = $context->findVariables($name);

        if ($this->lookups === []) {
            if ($context->options->strictVariables && $variables === []) {
                return new UndefinedVariable($this->toString());
            }

            return $variables[0] ?? null;
        }
```

and the rest of `evaluate()` (lines 103–135), which iterates the candidates and
uses `continue 2` to fall through to the next one when a lookup misses:

```php
        foreach ($variables as $object) {
            $object = $context->evaluate($object);

            if ($object instanceof \Generator) {
                $object = iterator_to_array($object, preserve_keys: false);
            }

            foreach ($this->lookups as $i => $lookup) {
                // ... lookup resolution ...

                if ($nextObject instanceof MissingValue) {
                    continue 2;
                }
                // ...
            }

            return $object;
        }

        return $context->options->strictVariables ? new UndefinedVariable($this->toString()) : null;
```

That `foreach` works unchanged over a `Generator` — this is why the change is
small.

### Other callers

`src/Drops/SelfDrop.php:20` and `:27` also call `findVariables()`. They are
**not** in scope: `findVariables()` keeps working exactly as before.

### Conventions

PHP 8.2+, PHPStan level 9 over `src/` (so the new method needs a
`@return \Generator<mixed>` docblock — the bare `\Generator` return type is not
enough at level 9), Laravel Pint formatting. `\Generator` is already used as a
return type across this codebase without a `use` import — see
`src/Nodes/BodyNode.php::stream()` and `src/Nodes/Variable.php::stream()`.
Match that: write `\Generator`, do not add an import.

## Commands you will need

| Purpose | Command | Expected on success |
|---------|---------|---------------------|
| Tests | `vendor/bin/pest` | `Tests: 800 passed` (or more), 0 failures |
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
  --output=json > build/plan-002.json

php tools/phpbench-compare.php build/base.json build/plan-002.json
```

`build/base.json` is the committed baseline at `4a1bdd6` — it is the baseline
for *all* plans, so this comparison shows the **cumulative** gain of plan 001 +
plan 002, expected around **+18% to +22%** on both subjects. To isolate this
plan's own contribution, compare against plan 001's result instead:
`php tools/phpbench-compare.php build/plan-001.json build/plan-002.json`
→ expected around **+6% to +9%** on both subjects.

If `build/plan-001.json` does not exist (plan 001 was run by someone else, or in
a different working copy), skip the isolated comparison and report only the
cumulative one.

Do not commit anything under `build/` other than the already-committed
`base.json`.

## Scope

**In scope** (the only files you should modify):
- `src/Render/RenderContext.php`
- `src/Nodes/VariableLookup.php`
- `tests/Integration/ContextTest.php` (add tests — see "Test plan")
- `plans/README.md` (status row only)

**Out of scope** (do NOT touch, even though they look related):
- `src/Drops/SelfDrop.php` — it calls `findVariables()`, which keeps its exact
  current signature and behaviour. Leave it alone.
- The `foreach ($variables as $object)` lookup-fallback loop in
  `VariableLookup::evaluate()` (lines 103 onward) — it already works over a
  `Generator` unchanged. Do not restructure it.
- `src/Render/RenderContext.php::internalContextLookup()` — that is plan 001.
- `build/base.json`.

## Git workflow

- Work on branch `perf/render-stream-optimizations` (created by plan 001; create
  it from `main` with `git switch -c perf/render-stream-optimizations` if it
  does not exist).
- One commit for this plan. Repo message style is plain sentence case, no
  conventional-commit prefixes. Use: `Resolve variables lazily instead of scanning every scope`.
- Do NOT push and do NOT open a PR.

## Steps

### Step 1: Add `iterateVariables()` and re-express `findVariables()` on top of it

In `src/Render/RenderContext.php`, replace the whole `findVariables()` method
with these two methods:

```php
    public function findVariables(string $key): array
    {
        return iterator_to_array($this->iterateVariables($key), preserve_keys: false);
    }

    /**
     * @return \Generator<mixed>
     */
    public function iterateVariables(string $key): \Generator
    {
        $found = false;

        // Check the variable in all scopes + env data + static variables
        $scopeCount = count($this->scopes);
        for ($index = 0; $index < $scopeCount + 2; $index++) {
            $scope = match (true) {
                $index < $scopeCount => $this->scopes[$index],
                $index === $scopeCount => $this->data,
                default => $this->sharedState->staticVariables,
            };

            $value = $this->internalContextLookup($scope, $key);

            if (! $value instanceof MissingValue) {
                $found = true;

                if ($value instanceof IsContextAware) {
                    $value->setContext($this);
                }

                yield $value;
            }
        }

        // Inject the implicit self drop only when no value (including explicit null) was found.
        // An explicit `self = nil` yields null before this point, so $found is true and the
        // fallback is skipped, correctly distinguishing defined-null from undefined.
        if (! $found && $key === 'self') {
            yield $this->getSelfDrop();
        }
    }
```

Two differences from the original that are intentional:

- `setContext()` now happens per value as it is yielded, rather than in a second
  pass over the collected array. Consumers see an identical result.
- The `self` fallback is driven by the `$found` flag instead of
  `$variables === []`, because a generator cannot inspect what it already
  yielded. This preserves the defined-null-vs-undefined distinction.

**Verify**: `vendor/bin/pest` → all pass. `findVariables()` is now a thin
wrapper, so every existing consumer must still be green at this point, before
any caller is switched over.

### Step 2: Switch `VariableLookup::evaluate()` to the lazy path

In `src/Nodes/VariableLookup.php`, change the head of `evaluate()` from the
"Current state" excerpt to:

```php
    public function evaluate(RenderContext $context): mixed
    {
        $name = $context->evaluate($this->name);
        assert(is_string($name));
        $variables = $context->iterateVariables($name);

        if ($this->lookups === []) {
            foreach ($variables as $variable) {
                return $variable;
            }

            return $context->options->strictVariables ? new UndefinedVariable($this->toString()) : null;
        }
```

Leave everything from `foreach ($variables as $object) {` onward exactly as it
is — it consumes the generator correctly without modification.

Why the `foreach`-then-`return` shape: it takes the first yielded value and
abandons the generator, so the remaining scopes are never probed. `null` is a
legitimate first value (an explicitly-null variable), which is why this cannot
be written as a null-coalescing expression.

**Verify**:
- `vendor/bin/pest` → all pass, same count as before.
- `vendor/bin/phpstan analyse --no-progress` → `[OK] No errors`.
- `vendor/bin/pint --test` → exit 0.

### Step 3: Add regression tests for the lazy path

See "Test plan" below for the exact cases.

**Verify**: `vendor/bin/pest --filter='lazy variable'` → the new tests pass.
Then `vendor/bin/pest` → full suite green with the new tests included.

### Step 4: Benchmark

Run the "Benchmark procedure" above. Record both delta percentages.

**Verify**: positive ops/s delta on both `benchRender` and `benchStream`
versus `build/base.json`.

### Step 5: Commit

```bash
git status
git add src/Render/RenderContext.php src/Nodes/VariableLookup.php tests/Integration/ContextTest.php plans/README.md
git commit -m "Resolve variables lazily instead of scanning every scope"
```

**Verify**: `git show --stat HEAD` → exactly the four files above.

## Test plan

Add three tests to `tests/Integration/ContextTest.php`. Model them structurally
on the tests already in that file — Pest `test('...', function () { ... })` with
`expect(...)->toBe(...)`; read two neighbouring tests first and match their
setup style for building a `RenderContext`.

The cases, each pinning one behaviour this change could plausibly break:

1. **"lazy variable resolution returns the innermost scope"** — the same name
   defined in an outer scope and in a pushed inner scope resolves to the inner
   value. Guards scan order.
2. **"lazy variable resolution distinguishes defined-null from undefined"** —
   a variable explicitly set to `null` resolves to `null` (not to a fallback,
   and not to an `UndefinedVariable` under `strictVariables`), while a name that
   was never set resolves to `null` normally and to `UndefinedVariable` under
   `strictVariables`. Guards the `$found` flag.
3. **"lazy variable resolution falls back when a lookup path misses"** — with
   `a` defined in two scopes, the inner one an array *without* key `b` and the
   outer one an array *with* key `b`, `a.b` resolves to the outer value. Guards
   the `continue 2` fallback that the generator must still support.

Case 3 is the important one: it is the only reason `findVariables()` collected
multiple candidates in the first place, and it is the behaviour a naive
"return the first match" rewrite would silently destroy. If `tests/Integration/ContextTest.php`
already covers it, say so in your report and skip adding a duplicate.

Also confirm the `self` fallback stays covered — `tests/Integration/SelfDropTest.php`
exercises it; it must pass untouched.

**Verification**: `vendor/bin/pest` → all pass, including 2–3 new tests.

## Done criteria

ALL must hold:

- [ ] `vendor/bin/pest` exits 0, with 2–3 more tests than before the change
- [ ] `vendor/bin/phpstan analyse --no-progress` reports `[OK] No errors`
- [ ] `vendor/bin/pint --test` exits 0
- [ ] `grep -n 'findVariables' src/Nodes/VariableLookup.php` returns no matches
- [ ] `grep -n 'public function findVariables' src/Render/RenderContext.php` still returns a match (the method was kept for BC)
- [ ] `php tools/phpbench-compare.php build/base.json build/plan-002.json` shows a positive delta on both `benchRender` and `benchStream`
- [ ] `git show --stat HEAD` lists only the four in-scope files
- [ ] `plans/README.md` status row for 002 updated to DONE

## STOP conditions

Stop and report back (do not improvise) if:

- The code at `src/Render/RenderContext.php:180-214` or
  `src/Nodes/VariableLookup.php:89-101` does not match the "Current state"
  excerpts (beyond plan 001's edit to `internalContextLookup()`).
- `tests/Integration/SelfDropTest.php` fails — that means the `self` fallback
  distinction broke, which is the subtlest risk in this plan.
- Any existing test fails. Do not modify existing tests to make them pass.
- The benchmark shows a negative delta versus `build/base.json` on either
  subject across two runs.
- You conclude the lookup-fallback loop needs restructuring to work with a
  generator. It does not — if it seems to, something else is wrong.

## Maintenance notes

- `findVariables()` and `iterateVariables()` must not drift apart. If a future
  change adds a scope source (a fourth place to look), it goes in
  `iterateVariables()` only; `findVariables()` stays a one-line wrapper.
- The generator is consumed twice-shaped but never rewound. A caller that needs
  the candidate list more than once must use `findVariables()`, not
  `iterateVariables()` — generators are single-pass. A reviewer should check any
  new consumer for that.
- `setContext()` is now called only on values that are actually reached. If some
  code depended on the old eager behaviour — every candidate getting a context,
  including ones never used — it would change. Nothing in this repo does; that
  was verified by grepping the callers of `findVariables()` before writing this
  plan.
- Deferred: `SelfDrop::__get()` and `__isset()` still call `findVariables()`
  eagerly (`src/Drops/SelfDrop.php:20`, `:27`). They are cold in the theme
  benchmark, so switching them was left out to keep this diff small.
