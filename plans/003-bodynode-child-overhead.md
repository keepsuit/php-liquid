# Plan 003: Remove per-child overhead in the `BodyNode` render and stream loops

> **Executor instructions**: Follow this plan step by step. Run every
> verification command and confirm the expected result before moving to the
> next step. If anything in the "STOP conditions" section occurs, stop and
> report — do not improvise. When done, update the status row for this plan
> in `plans/README.md`.
>
> **Drift check (run first)**: `git diff --stat 4a1bdd6..HEAD -- src/Nodes/BodyNode.php src/Render/RenderContext.php`
> `src/Nodes/BodyNode.php` must be unchanged since `4a1bdd6` — plans 001 and 002
> do not touch it. `src/Render/RenderContext.php` will show plan 001's and plan
> 002's edits; that is expected. Any change to `hasInterrupt()` or to
> `BodyNode` is a STOP condition.

## Status

- **Priority**: P1
- **Effort**: S
- **Risk**: LOW
- **Depends on**: plans/001, plans/002 (ordering only — not a hard dependency)
- **Category**: perf
- **Planned at**: commit `4a1bdd6`, 2026-07-29

## Why this matters

`BodyNode` is the loop that walks every node of a template. One render pass of
the theme benchmark runs it 361 times over **4384 child nodes**, so anything
paid per child is paid thousands of times. Four things are paid per child today
and do not need to be:

1. **`streamChild()` allocates a `Generator` for every non-streamable node.**
   Text nodes and most tags are not `CanBeStreamed`, so streaming a typical
   template allocates one generator object per node just to yield a single
   string, plus the `yield from` delegation on top.
2. **`$node instanceof Tag` fires a method call on every tag node.**
   `Tag::ensureTagIsEnabled()` (`src/Tag.php`) starts with
   `if (! $this instanceof Disableable) { return; }` — so for the vast majority
   of tags the call exists only to return immediately. Testing `Disableable`
   at the call site skips it.
3. **`renderChild()` is an indirection with no purpose** — it has no overrides
   anywhere in `src/`, `tests/`, or `performance/` (verified by grep) and does
   nothing but forward to `$node->render($context)`.
4. **`hasInterrupt()` calls `count()`** on an array that is empty in the common
   case, once per child, where a `!== []` comparison suffices.

Measured as a bundle on top of plans 001 and 002: **−5.9% render, −9.4% stream**.
The four items were measured together, not individually — item 1 is
stream-only, items 2–4 affect both loops.

## Current state

### `src/Nodes/BodyNode.php` — the node-walking loops

Exact code today, `src/Nodes/BodyNode.php:45-127`:

```php
    /**
     * @throws LiquidException
     */
    public function render(RenderContext $context): string
    {
        $context->resourceLimits->incrementRenderScore(count($this->children));

        $output = '';

        foreach ($this->children as $node) {
            try {
                if ($node instanceof Tag) {
                    $node->ensureTagIsEnabled($context);
                }

                $output .= $this->renderChild($context, $node);
            } catch (UndefinedVariableException|UndefinedDropMethodException|UndefinedFilterException $exception) {
                $context->handleError($exception, $node->lineNumber);
            } catch (\Throwable $exception) {
                $output .= $context->handleError($exception, $node->lineNumber);
            }

            if ($context->hasInterrupt()) {
                break;
            }
        }

        $context->resourceLimits->incrementWriteScore($output);

        return $output;
    }

    /**
     * @return \Generator<string>
     *
     * @throws LiquidException
     */
    public function stream(RenderContext $context): \Generator
    {
        $context->resourceLimits->incrementRenderScore(count($this->children));

        foreach ($this->children as $node) {
            try {
                if ($node instanceof Tag) {
                    $node->ensureTagIsEnabled($context);
                }

                foreach ($this->streamChild($context, $node) as $output) {
                    $context->resourceLimits->incrementWriteScore($output);
                    yield $output;
                }
            } catch (UndefinedVariableException|UndefinedDropMethodException|UndefinedFilterException $exception) {
                $context->handleError($exception, $node->lineNumber);
            } catch (\Throwable $exception) {
                $output = $context->handleError($exception, $node->lineNumber);
                $context->resourceLimits->incrementWriteScore($output);
                yield $output;
            }

            if ($context->hasInterrupt()) {
                break;
            }
        }
    }

    protected function renderChild(RenderContext $context, Node $node): string
    {
        return $node->render($context);
    }

    /**
     * @return \Generator<string>
     */
    public function streamChild(RenderContext $context, Node $node): \Generator
    {
        if ($node instanceof CanBeStreamed) {
            yield from $node->stream($context);

            return;
        }

        yield $node->render($context);
    }
```

The file's current imports (`src/Nodes/BodyNode.php:5-11`):

```php
use Keepsuit\Liquid\Contracts\CanBeStreamed;
use Keepsuit\Liquid\Exceptions\LiquidException;
use Keepsuit\Liquid\Exceptions\UndefinedDropMethodException;
use Keepsuit\Liquid\Exceptions\UndefinedFilterException;
use Keepsuit\Liquid\Exceptions\UndefinedVariableException;
use Keepsuit\Liquid\Render\RenderContext;
use Keepsuit\Liquid\Tag;
```

### `src/Tag.php` — why the `Disableable` check is equivalent

```php
    public function ensureTagIsEnabled(RenderContext $context): void
    {
        if (! $this instanceof Disableable) {
            return;
        }

        if (! $context->tagDisabled(static::tagName())) {
            return;
        }

        throw new TagDisabledException(static::tagName());
    }
```

The method is a no-op unless `$this instanceof Disableable`. `Disableable` lives
at `Keepsuit\Liquid\Contracts\Disableable`.

### `src/Render/RenderContext.php` — the interrupt check

```php
    public function hasInterrupt(): bool
    {
        return count($this->interrupts) > 0;
    }
```

`$this->interrupts` is declared as `protected array $interrupts = [];` in the
same file, so `!== []` is exactly equivalent to `count(...) > 0`.

### Conventions

PHP 8.2+, PHPStan level 9 over `src/`, Laravel Pint. Note the level-9
constraint on step 2: `ensureTagIsEnabled()` is declared on `Tag`, not on
`Disableable`, so testing `Disableable` alone makes PHPStan reject the call.
The plan's code keeps both checks — `Disableable` first (it filters out almost
everything at zero cost), `Tag` second (it satisfies the analyser).

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
  --output=json > build/plan-003.json

php tools/phpbench-compare.php build/base.json build/plan-003.json
```

Versus `build/base.json` this is the **cumulative** result of all three plans:
expected around **+19% on `benchRender` and +26% on `benchStream`**.

To isolate this plan: `php tools/phpbench-compare.php build/plan-002.json build/plan-003.json`
→ expected roughly **+5% render, +9% stream**. Skip the isolated comparison if
`build/plan-002.json` does not exist.

Do not commit anything under `build/` other than the already-committed
`base.json`.

## Scope

**In scope** (the only files you should modify):
- `src/Nodes/BodyNode.php`
- `src/Render/RenderContext.php` (the `hasInterrupt()` method only)
- `plans/README.md` (status row only)

**Out of scope** (do NOT touch, even though they look related):
- `src/Tag.php` — `ensureTagIsEnabled()` keeps its internal `Disableable`
  guard. It is public API and is called from elsewhere; the call-site check is
  an addition, not a replacement.
- **Deleting `renderChild()` or `streamChild()`** — they become unused, but
  `streamChild()` is `public` and `renderChild()` is `protected`, so removing
  either is a breaking change for any downstream subclass. Leave both in place;
  see "Maintenance notes".
- The `try`/`catch` structure and the error-handling branches in both loops —
  do not restructure them.
- `incrementRenderScore()` / `incrementWriteScore()` call sites and their
  ordering — the stream loop must keep scoring each chunk before yielding it.
- `src/Render/RenderContext.php` beyond `hasInterrupt()`.

## Git workflow

- Work on branch `perf/render-stream-optimizations` (created by plan 001; create
  it from `main` with `git switch -c perf/render-stream-optimizations` if it
  does not exist).
- One commit for this plan. Repo message style is plain sentence case, no
  conventional-commit prefixes. Use: `Trim per-child overhead in body rendering`.
- Do NOT push and do NOT open a PR.

## Steps

### Step 1: Inline the non-streamable child case in `stream()`

In `src/Nodes/BodyNode.php::stream()`, replace:

```php
                foreach ($this->streamChild($context, $node) as $output) {
                    $context->resourceLimits->incrementWriteScore($output);
                    yield $output;
                }
```

with:

```php
                if ($node instanceof CanBeStreamed) {
                    foreach ($node->stream($context) as $output) {
                        $context->resourceLimits->incrementWriteScore($output);
                        yield $output;
                    }
                } else {
                    $output = $node->render($context);
                    $context->resourceLimits->incrementWriteScore($output);
                    yield $output;
                }
```

This is the same dispatch `streamChild()` performed, minus the intermediate
generator. Both branches stay inside the existing `try` block, so error handling
is unchanged.

**Verify**: `vendor/bin/pest --filter=Stream` → all stream tests pass
(`tests/Integration/StreamTest.php` covers chunk boundaries, which is exactly
what this step could break).

### Step 2: Check `Disableable` at the call site in both loops

In `src/Nodes/BodyNode.php`, in **both** `render()` and `stream()`, replace:

```php
                if ($node instanceof Tag) {
                    $node->ensureTagIsEnabled($context);
                }
```

with:

```php
                if ($node instanceof Disableable && $node instanceof Tag) {
                    $node->ensureTagIsEnabled($context);
                }
```

Keep that operand order: `Disableable` first is the point of the change, `Tag`
second is what keeps PHPStan level 9 happy about the method call.

Add the import alongside the existing ones at the top of the file:

```php
use Keepsuit\Liquid\Contracts\Disableable;
```

Keep `use Keepsuit\Liquid\Tag;` — it is still referenced by the second operand.

**Verify**:
- `vendor/bin/phpstan analyse --no-progress` → `[OK] No errors`
- `vendor/bin/pest` → all pass. Disabled-tag behaviour is covered by the
  existing suite; if a test about disabled tags fails here, the operand order or
  the `Disableable` import is wrong.

### Step 3: Drop the `renderChild()` indirection at the call site

In `src/Nodes/BodyNode.php::render()`, replace:

```php
                $output .= $this->renderChild($context, $node);
```

with:

```php
                $output .= $node->render($context);
```

Leave the `renderChild()` method definition in the file — see "Out of scope".

**Verify**: `vendor/bin/pest` → all pass.

### Step 4: Simplify `hasInterrupt()`

In `src/Render/RenderContext.php`:

```php
    public function hasInterrupt(): bool
    {
        return $this->interrupts !== [];
    }
```

**Verify**: `vendor/bin/pest --filter='break|continue'` → interrupt-related
tests pass. Then `vendor/bin/pest` → full suite green.

### Step 5: Formatting, benchmark, commit

```bash
vendor/bin/pint --test
```

Then run the "Benchmark procedure" above and record both delta percentages.

```bash
git status
git add src/Nodes/BodyNode.php src/Render/RenderContext.php plans/README.md
git commit -m "Trim per-child overhead in body rendering"
```

**Verify**: `git show --stat HEAD` → exactly three files.

## Test plan

No new tests are required — every step is behaviour-preserving and the existing
suite already covers each affected path:

- `tests/Integration/StreamTest.php` — streaming chunk boundaries. Step 1
  changes how chunks are produced, so this is the file that would catch a
  mistake there. Note that streaming semantics include *how many* chunks a
  template yields (the tests assert `toHaveCount(2)` and per-index values), so
  a merged or split chunk fails loudly. That is intended: step 1 must not change
  chunk boundaries.
- `tests/Unit/Tags/` and `tests/Integration/Tags/` — disabled-tag behaviour for
  step 2.
- `tests/Unit/ResourceLimitsTest.php` — render/write scoring, which step 1
  moves the call sites of.
- Break/continue interrupt tests for step 4.

If you would like one extra guard, add a test to `tests/Integration/StreamTest.php`
asserting that a template mixing text, an `{% if %}` block, and an output tag
streams the same chunk sequence before and after — but only if you can write it
without changing any existing test.

**Verification**: `vendor/bin/pest` → all pass, count unchanged (or +1).

## Done criteria

ALL must hold:

- [ ] `vendor/bin/pest` exits 0 with the same test count as before (or +1)
- [ ] `vendor/bin/phpstan analyse --no-progress` reports `[OK] No errors`
- [ ] `vendor/bin/pint --test` exits 0
- [ ] `grep -n 'streamChild($context' src/Nodes/BodyNode.php` returns no matches (the call is gone; the method definition remains)
- [ ] `grep -n 'renderChild($context' src/Nodes/BodyNode.php` returns no matches
- [ ] `grep -n 'count($this->interrupts)' src/Render/RenderContext.php` returns no matches
- [ ] `grep -n 'protected function renderChild' src/Nodes/BodyNode.php` still returns a match (kept for BC)
- [ ] `php tools/phpbench-compare.php build/base.json build/plan-003.json` shows a positive delta on both subjects
- [ ] `git show --stat HEAD` lists only the three in-scope files
- [ ] `plans/README.md` status row for 003 updated to DONE

## STOP conditions

Stop and report back (do not improvise) if:

- `src/Nodes/BodyNode.php` differs from the "Current state" excerpt.
- Any test in `tests/Integration/StreamTest.php` fails — that means step 1
  changed chunk boundaries, which it must not.
- PHPStan reports an error about `ensureTagIsEnabled()` not existing. The fix is
  the operand order given in step 2; if that does not resolve it, stop rather
  than adding a baseline entry or a `@phpstan-ignore` comment.
- Any test fails and the only way you can see to make it pass is editing the
  test.
- The benchmark shows a negative delta on either subject across two runs.

## Maintenance notes

- `renderChild()` and `streamChild()` are now dead code kept only for backward
  compatibility with downstream subclasses. Neither has an override anywhere in
  this repo. They are the right thing to delete in the next major release — flag
  that to the maintainer rather than doing it here.
- Because `streamChild()` is no longer called, a subclass that overrode it to
  customise streaming would silently stop taking effect. That is the one real
  behavioural risk in this plan, and it is why deleting the method now (which
  would fail loudly instead) is a defensible alternative the maintainer may
  prefer. Raise it; do not decide it yourself.
- The `Disableable && Tag` check duplicates the guard inside
  `Tag::ensureTagIsEnabled()`. If that method ever grows behaviour that must run
  for non-`Disableable` tags, the call-site check has to come out again.
- Deferred: coalescing runs of adjacent `Text` nodes at parse time would cut the
  per-child cost further for both loops, but it is a parser-phase change and was
  out of scope for this render-phase audit.
