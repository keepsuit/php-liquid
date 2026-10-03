# Plan 001: Implement Twig-shaped compiled output without changing Liquid semantics

> **Executor instructions**: Follow this plan step by step. Run every
> verification command and confirm the expected result before moving to the
> next step. If anything in the "STOP conditions" section occurs, stop and
> report — do not improvise. When done, update the status row for this plan in
> `plans/README.md`.
>
> **Drift check (run first)**: `git diff --stat 707dd35..HEAD -- src/Compiler src/Nodes/Document.php src/Nodes/BodyNode.php src/Nodes/Variable.php src/Nodes/VariableLookup.php tests/Integration/CompilerTest.php tests/Integration/CompilerOutputTest.php`
> The planned SHA is the current clean checkout. If any listed path changed,
> compare the excerpts below with live code before proceeding.

## Status

- **Priority**: P1
- **Effort**: L
- **Risk**: MED
- **Depends on**: none
- **Category**: tech-debt / dx / perf
- **Planned at**: commit `707dd35`, 2026-08-03

## Why this matters

The compiler currently emits valid artifacts, but a small ten-line storefront
snippet expands into per-variable fallback properties, a generated constructor,
fully qualified names, nested output accumulators, a `do { } while (false)`
escape hatch, and repeated string concatenations. That makes generated PHP hard
to inspect and hides the relationship between the Liquid source and its output.
Make common text and variable nodes read like the Twig reference—one readable
compiled method with literal template segments and explicit dynamic expressions—
while preserving Liquid’s error handling, scope lookup, filters, interrupts,
resource limits, runtime partials, and require-able artifact behavior.

## Current state

The relevant files are:

- `src/Compiler/Compiler.php` — assembles the generated namespace, class,
  fallback properties, render method, body methods, and return statement.
- `src/Compiler/CompilerContext.php` — emits node statements, error guards,
  output accumulators, and runtime fallback properties.
- `src/Nodes/BodyNode.php` — emits a per-body accumulator and interrupt bailout.
- `src/Nodes/Variable.php` and `src/Nodes/VariableLookup.php` — own Liquid
  lookup, filter, rendering, strict-variable, and stringification semantics.
- `src/Compiler/CompiledTemplate.php` — preserves the public compiled-template
  contract: `render()` returns a string and `stream()` yields that string once.
- `tests/Integration/CompilerTest.php` — existing compiled parity, fallback,
  source-shape, error, stream, and resource-limit coverage.
- `performance/themes/storefront/snippets/product/specs.liquid` — the
  ten-line representative fixture used for the requested output shape.

The current compiler header and method assembly are fully qualified and use
`$output0` (`src/Compiler/Compiler.php:46-124`):

```php
->writeLine('namespace Keepsuit\\Liquid\\Compiler\\Generated;')
->writeLine('if (! class_exists('.$className.'::class, false)) {')
->writeLine('final class '.$className.' extends \\Keepsuit\\Liquid\\Compiler\\CompiledTemplate')
...
->writeLine('protected function renderCompiled(\\Keepsuit\\Liquid\\Render\\RenderContext $context): string')
...
$builder->writeLine('$output0 = \'\';');
```

The current body compiler always pushes a new output scope, treats every
non-text child as potentially interruptible, and appends every child separately
(`src/Nodes/BodyNode.php:57-105`). The output writer is a plain accumulator
(`src/Compiler/CompilerContext.php:121-143`), and unsupported nodes are kept as
constructor properties and rendered through the generic fallback
(`src/Compiler/CompilerContext.php:145-196` and `250-268`).

The current `Variable` is intentionally not a `CanBeCompiled` node; it is
reconstructed as an exported runtime object (`src/Nodes/Variable.php:27-47` and
`tests/Integration/CompilerTest.php:711-724`). Do not make it claim to compile
itself. Add a compiler-owned direct-expression path that reuses its exact
runtime semantics, and retain the existing property fallback for complex
expressions.

For the fixture at `performance/themes/storefront/snippets/product/specs.liquid:1-10`,
the current generated artifact contains six `private readonly mixed $valueN`
properties and a constructor rebuilding `Variable`/`VariableLookup` objects,
then emits a `$output1` accumulator with one try/catch block per value. The
target shape is structurally like this (class hash and exact helper arguments
are generated):

```php
use Keepsuit\Liquid\Compiler\CompiledTemplate;
use Keepsuit\Liquid\Render\RenderContext;
use Keepsuit\Liquid\TemplateSharedState;

final class Template_<hash> extends CompiledTemplate
{
    protected function renderCompiled(RenderContext $context): string
    {
        $output = '<table class="specs">\n'
            .'  <caption>Details</caption>\n';
        // line 4
        try {
            $output .= $this->renderCompiledVariable($context, 'product', ['vendor'], []);
        } catch (...) {
            // Existing Liquid error handling remains here.
        }

        return $output;
    }
}
```

This is a target shape, not a snapshot. Keep the `class_exists(..., false)` guard
unless repeated `require` coverage proves the artifact-loading contract can be
changed safely. Do not copy Twig-only `$env`, `Source`, `$blocks`, `$macros`,
`TemplateWrapper`, sandbox imports, or `getSourceContext()` APIs: Liquid’s
`ParsedTemplate` retains a document/name but not the original source text
(`src/ParsedTemplate.php:9-16`, `src/Parse/ParseContext.php:76-93`). Keep the
Liquid filter name `size`; the reference’s `length` is a Twig syntax change,
not part of this compiler-formatting work.

The exact Twig generator contract is deliberately not part of this plan.
`CompiledTemplate::render()` requires a string and `stream()` yields that string
once (`src/Compiler/CompiledTemplate.php:17-44`); the current implementation was
also explicitly optimized to avoid a generator per nesting level. If exact
`yield`/`doDisplay` output is required, stop and split that into a separate API
and benchmark design.

## Commands you will need

| Purpose | Command | Expected on success |
|---------|---------|---------------------|
| Focused compiler tests | `vendor/bin/pest tests/Integration/CompilerTest.php tests/Integration/CompilerArtifactSafetyTest.php tests/Integration/Performance/StorefrontThemeTest.php` | 62 tests pass before the change; all pass after it |
| Full tests | `composer test` | 888 baseline tests pass; no regressions after the change |
| Formatting check | `vendor/bin/pint --test` | `PASS`, no files to format |
| Static analysis | `vendor/bin/phpstan analyse --no-progress` | `[OK] No errors` |
| Diff hygiene | `git diff --check` | no output, exit 0 |
| Compiler benchmark | `vendor/bin/phpbench run --group=compiler --warmup=1 --retry-threshold=5 --report=aggregate --output=json > /tmp/php-liquid-compiler-after.json` | exit 0 and valid aggregate JSON |

The repository’s compiler benchmark documentation says to compare matching
aggregate rows and treat throughput regressions above 5% as a review failure
(`performance/README.md:46-66`). Establish a same-environment baseline before
using the comparator; a single high-variance or near-threshold run is not enough
to accept a performance change.

## Scope

**In scope** (the only source/test files to modify):

- `src/Compiler/Compiler.php`
- `src/Compiler/CompilerContext.php`
- `src/Compiler/CompiledTemplate.php`
- `src/Compiler/CodeBuilder.php` only if the multiline literal writer belongs
  there rather than in `CompilerContext`
- `src/Nodes/Document.php` — only to mark the document body as the root output
  scope
- `src/Nodes/BodyNode.php`
- `src/Nodes/Variable.php`
- `src/Nodes/VariableLookup.php`
- `tests/Integration/CompilerTest.php`
- `tests/Integration/CompilerOutputTest.php` (create only if separating source
  shape tests from the already large compiler integration file is cleaner)

**Out of scope** (do not touch):

- `src/Environment.php` and artifact publication/atomicity behavior.
- `src/Template.php`, `src/AbstractTemplate.php`, and the public
  `CompiledTemplate::render()`/`stream()` contract.
- `src/Tags/RenderTag.php`, `src/Tags/ForTag.php`, or runtime partial lookup;
  v1 partials remain runtime-linked.
- `performance/themes/storefront/*`, including the `size` filter in the fixture.
- Twig dependencies or Twig runtime classes.
- Snapshotting all 29 storefront templates; the benchmark documentation
  deliberately rejects snapshots for this fast-changing fixture.
- Any file outside the list above, unless a STOP condition is reported first.

## Git workflow

- Match the repository’s existing conventional-commit style, for example
  `refactor: ...` or `perf: ...` from the recent compiler history.
- Do not push or open a PR unless the operator instructs you to do so.
- Keep generated benchmark artifacts under `/tmp` or the repository’s ignored
  cache paths; do not add generated PHP artifacts to the repository.

## Steps

### Step 1: Add parity and output-shape characterization tests

Extend the existing compiler integration coverage (or create
`tests/Integration/CompilerOutputTest.php`) with focused tests for the requested
fixture and the direct-expression boundary:

1. Parse `snippets.product.specs` through
   `Keepsuit\Liquid\Performance\Support\StorefrontTheme::environment()`.
2. Compile it to the existing temporary artifact path helper pattern.
3. Assert the compiled artifact renders exactly the same bytes as the parsed
   template using the product page data from
   `StorefrontTheme::renderData('templates.product')['page']`.
4. Assert `implode('', iterator_to_array($compiled->stream(...)))` matches the
   interpreted render.
5. Assert the generated source contains the generated `use` imports, extends
   the imported `CompiledTemplate`, contains `renderCompiledVariable`, has a
   `// line 4` marker, and contains the `size` filter descriptor.
6. Assert the specs artifact has no `private readonly mixed $valueN` properties,
   no `new \Keepsuit\Liquid\Nodes\Variable(` constructor rebuilds, and no
   `do {` block caused only by ordinary variable output.
7. Add a complex-expression case to prove fallback remains available, such as a
   dynamic lookup key or a filter argument containing a `VariableLookup`; assert
   parity and the presence of the existing fallback property path.
8. Require the same simple artifact twice in one process and assert both results
   are `CompiledTemplate` instances, preserving the class guard contract.

Do not assert the entire generated file as a snapshot. Assert stable structural
markers and behavior, following the existing source-shape assertions at
`tests/Integration/CompilerTest.php:635-660` and the parity style at
`tests/Integration/CompilerTest.php:198-230`.

**Verify**: `vendor/bin/pest tests/Integration/CompilerTest.php tests/Integration/CompilerArtifactSafetyTest.php tests/Integration/Performance/StorefrontThemeTest.php` → existing tests pass; only newly added target-shape assertions may fail until Steps 2–3 are complete.

### Step 2: Add a direct common-variable emission seam without changing Liquid semantics

Refactor `VariableLookup` and `Variable` so interpreted rendering and generated
common-variable rendering share the same implementation:

1. Extract the current lookup walk from `VariableLookup::evaluate()` into a
   callable/static path evaluator that accepts a root name and the parsed lookup
   segments. Preserve all existing behavior: scope-chain fallback when an inner
   lookup breaks, `MissingValue`, strict undefined-variable errors, dynamic
   lookup segments when the interpreted path is used, generators,
   `IsContextAware`, and the implicit `size`/`first`/`last` lookup filters.
2. Extract filter application and output stringification from
   `Variable::evaluate()`/`render()` into reusable methods. The shared path must
   preserve generator materialization before filters, positional plus named
   filter arguments, `CanBeRendered`, booleans, numerics, arrays, objects with
   `__toString()`, and null output.
3. Add a protected helper on `CompiledTemplate`, named
   `renderCompiledVariable(RenderContext $context, string $name, array $lookups, array $filters): string`,
   which calls those shared methods. It must not cache context-bound objects or
   bypass `RenderContext`.
4. Add a concrete `CompilerContext` special case for `Variable` nodes without
   making `Variable` implement `CanBeCompiled`: emit the helper call inside the
   same per-node error guard used by fallback nodes.
5. Emit the direct path only when the variable root is a `VariableLookup`, all
   lookup segments are scalar string/int values, and all filter arguments are
   safely exportable scalar expressions. If a name, lookup, or filter argument
   contains a complex `CanBeEvaluated` object, fall back to the existing
   `writeRuntimeValue()` property and `->render($context)` path.
6. Keep the line number in the generated error guard and add a line comment
   immediately before each dynamic emission, matching the useful part of the
   Twig output without introducing Twig’s source-context API.

The direct emitter must be a runtime seam, not a PHP-native `$object->property`
shortcut: Liquid lookup semantics differ from Twig and include outer-scope
fallbacks, Drops, strict errors, context-aware values, and implicit lookup
filters.

**Verify**: `vendor/bin/pest tests/Integration/CompilerTest.php tests/Integration/CompilerArtifactSafetyTest.php` → all existing compiled parity/error/resource-limit tests plus the new direct/fallback cases pass.

### Step 3: Simplify the generated writer around the new seam

Update the code writer while keeping the runtime behavior from Step 2:

1. Emit `use` statements for the generated class’s fixed runtime types and use
   short names in the class declaration, constructor, and render method.
2. Keep the repeated-`require` `class_exists(..., false)` guard and the current
   `return new Template_<hash>;` artifact contract.
3. Rename the depth-zero accumulator to `$output` and compile the document body
   directly into that root accumulator; retain numbered accumulators only for
   nested body methods that need their own resource-accounting scope. Use an
   explicit root-body marker from `Document::compile()`/`CompilerContext`, not
   a heuristic based only on output depth, so nested bodies remain isolated.
4. Coalesce adjacent `Text`/`Raw` literal nodes in `BodyNode` before emitting a
   dynamic node. Use a safe multiline PHP literal writer for printable template
   text containing newlines, falling back to `VarExporter` for control-heavy
   strings so quotes, backslashes, null bytes, and PHP-looking text remain data.
5. Add a conservative interruptability check: ordinary `Text`, `Raw`, and
   direct `Variable` emissions cannot push Liquid interrupts, so do not emit a
   `do { } while (false)` wrapper or `hasInterrupt()` check for those nodes;
   retain the existing bailout for unknown/fallback nodes and control-flow tags.
6. Generate a constructor only when fallback properties are still required;
   the specs artifact should therefore have no generated constructor.
7. Do not remove resource-limit accounting, per-node error handling, runtime
   fallback properties, method bodies used by `for`, or partial behavior merely
   to reduce line count.

**Verify**: `vendor/bin/pest tests/Integration/CompilerTest.php tests/Integration/CompilerOutputTest.php` → the source-shape assertions pass and the compiled output remains byte-for-byte equal to interpreted output for all covered cases.

### Step 4: Run the full safety and performance gates

Run the focused tests, then the full suite and static checks. Capture a compiler
benchmark JSON before and after the implementation with identical PHPBench
settings. Compare only matching aggregate rows; if no `main` compiler baseline
exists, record that the result is branch-only instead of inventing a conclusion.

**Verify**:

- `composer test` → all tests pass; no new failures.
- `vendor/bin/pint --test` → formatting check passes.
- `vendor/bin/phpstan analyse --no-progress` → `[OK] No errors`.
- `git diff --check` → exit 0 with no output.
- `vendor/bin/phpbench run --group=compiler --warmup=1 --retry-threshold=5 --report=aggregate --output=json > /tmp/php-liquid-compiler-after.json` → valid aggregate JSON and no reproducible throughput regression above the repository’s 5% threshold.
- `git status --short` → only the in-scope source/test files are modified, plus the executor’s allowed status update in `plans/README.md`.

## Test plan

- Keep all existing compiler tests in `tests/Integration/CompilerTest.php`,
  especially control-flow parity, partial fallback, interrupts, resource
  limits, repeated state, unsafe fallback reconstruction, and extension tests.
- Add a focused storefront-spec source-shape/parity test as described in Step 1.
- Add direct-variable parity cases covering a plain lookup, nested lookup,
  scalar filter (`size`), strict missing variable, a `CanBeRendered` value, and
  a complex lookup/filter argument that deliberately uses fallback.
- Keep literal safety coverage for quotes, escapes, control characters, PHP
  looking text, and multiline HTML.
- Verify the existing performance fixture test still passes; it already renders
  every page with strict variables, strict filters, and rethrown errors
  (`tests/Integration/Performance/StorefrontThemeTest.php:53-74`).

## Done criteria

- [ ] The specs artifact has imported runtime names, one readable compiled render
      method, coalesced literal segments, line comments, and direct common
      variable calls; it has no fallback properties for those six simple values.
- [ ] Complex variables and unsupported tags still use the existing safe runtime
      fallback and constructor-property path.
- [ ] `render()` and `stream()` preserve the public string/one-chunk contract.
- [ ] Parsed and compiled renders, stream byte values, errors, interrupts,
      resource limits, state persistence, and runtime partial lookup remain equal.
- [ ] `composer test` exits 0.
- [ ] `vendor/bin/pint --test` exits 0.
- [ ] `vendor/bin/phpstan analyse --no-progress` exits 0 with no errors.
- [ ] `git diff --check` exits 0.
- [ ] The compiler benchmark has a matching baseline or is explicitly recorded
      as branch-only; no reproducible regression above 5% is accepted.
- [ ] No files outside the Scope list are modified.
- [ ] `plans/README.md` status row is updated to `DONE`, or `BLOCKED` with the
      concrete reason.

## STOP conditions

Stop and report back instead of improvising if:

- The compiler contract, `Variable`/`VariableLookup` semantics, or the current
  generated source no longer matches the Current state excerpts.
- Exact Twig `yield`/`doDisplay` output is required; that needs a separate
  decision about the `Template` and benchmark contracts.
- The direct helper cannot preserve outer-scope lookup fallback, strict errors,
  implicit lookup filters, Drop context binding, or `CanBeRendered` behavior.
- Removing an interrupt check changes any existing break/continue/partial parity
  test, or resource-limit counters differ from interpreted rendering.
- A simple artifact can be required only once after the class-header cleanup;
  restore the guard and report rather than changing cache semantics.
- A benchmark shows a reproducible throughput regression above 5%, or a result
  is too high-variance to classify after two representative runs.
- A change appears to require touching an out-of-scope file.
- Any verification command fails twice after a reasonable fix attempt.

## Maintenance notes

- The direct-variable helper is a compiler/runtime seam; any future Liquid
  lookup or filter-semantic change must update both interpreted and compiled
  tests before changing its implementation.
- Keep the conservative fallback boundary. New tags/nodes should remain runtime
  fallback unless their semantics can be expressed without bypassing context,
  error, interrupt, or resource-limit handling.
- Do not turn generated output into a full-file snapshot. Assert stable source
  markers and use the existing storefront strict-render tests for fixture
  correctness.
- A future exact Twig-style generator would need a separate plan covering
  `CompiledTemplate`, stream chunk semantics, nested body methods, benchmark
  subjects, and an explicit performance comparison; it is not a follow-up to
  this formatting cleanup.
