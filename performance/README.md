# Benchmark suite

Two tools, two jobs:

- **[Tally](https://github.com/phpstan/tally)** gates pull requests. It counts
  instructions, cycles and memory under a CPU simulation, so the same code gives
  the same numbers on every run. It answers *"did this PR add work?"*.
- **PHPBench** measures wall-clock time locally. It answers *"is it actually
  faster?"*, which counts cannot: allocations, cache misses and file I/O cost time
  that instructions do not show.

| Benchmark | Tally (CI) | PHPBench (local) |
| --- | --- | --- |
| `OperationBench` | per-feature gate | when needed |
| `ThemeBench` | whole-pipeline gate | confirm speedups, compare interpreted vs compiled, profile |
| `GrowthBench` | complexity bounds | — |
| `TemplateCacheBench` | — (file I/O) | yes |

```bash
composer benchmark             # default group: the storefront theme
composer benchmark:cache       # cache group: template-cache backends
composer benchmark:operations  # operations group: individual operations
php performance/profile-theme.php --output=profile.json
```

## What each group is for

The benchmark groups have different jobs, and conflating them is how a benchmark suite
stops being useful.

**`default`** (`ThemeBench`) renders the storefront theme — 29 templates across
four pages — with both interpreted `benchRender` and precompiled
`benchRenderCompiled` subjects, plus `benchTokenize`, `benchParse` and `benchCompile`
(code generation only, no filesystem). Compilation and artifact loading happen during
setup, outside the timed compiled-render subject. It answers *"did rendering get
slower"* and nothing more. It cannot tell you *what* got slower, because a
regression in any one tag is averaged across everything else. Don't expect it to
localize.

**`cache`** (`TemplateCacheBench`) runs the same two subjects for every backend
(in-memory, serialize, var-export, compiled): `benchBuild*` parses the theme into an
empty cache, and `benchLoadAndRender*` builds a fresh environment per revolution, as
a new request would, then loads the warm cache and renders every page.

**`operations`** (`OperationBench`) measures single operations on tiny templates.
This is where per-feature sensitivity lives, and where a benchmark is allowed to
be unrealistic: an artificial template that does one thing 64 times is a better
instrument than a realistic page.

The split is what lets the theme be realistic. Whenever realism and measurement
sensitivity conflict inside the theme, realism wins — sensitivity is not the
theme's job.

Benchmarks run with OPcache enabled and JIT disabled, in both tools (`phpbench.json`, `tally.php`)
and in CI. With JIT, short-lived benchmark processes mostly measure JIT compilation of
the large generated template functions.

**`GrowthBench`** (`performance/tally/`, Tally only) checks how cost grows with
input size instead of pinning numbers: a `limit`ed range must stay constant from
10 to 1M items, and a product loop and `uniq` on nested lists must stay at most
linear from 4 to 96 items. It lives outside `performance/benchmarks` because
PHPBench cannot run Tally data providers.

## Tally in CI

`tally.yml` runs two jobs on every pull request:

- **PHP 8.5** compares every case with the `#[Expected]` numbers committed next to
  it, and checks the `GrowthBench` bounds. Tolerance: ±2 % instructions, ±5 %
  cycles, ±5 % memory.
- **PHP 8.2–8.4** have no committed numbers. Each job measures the base and the PR
  in the same run, writes the base numbers into the PR's benchmark files and
  compares the PR against them.

Tally needs Linux: `tally.php` configures it, and locally it runs in a
`php:8.5-cli` container. Local numbers differ from CI, so they are never
committed.

### Updating `#[Expected]`

Update the numbers only when a change is known and intended, in the same PR that
causes it:

- **slower**: find the cause first. Accept only if it is the price of the change,
  and say why in the PR description. Otherwise it is a regression.
- **faster**: accept, or a later regression back to the old numbers goes unnoticed.
- **new**, or the benchmark itself changed: accept.
- **the environment changed** (runner image, PHP patch release, extensions, ini,
  Tally version): accept in a dedicated PR with no other changes.

Never accept just to turn CI green, in a later PR, or for drift inside the
tolerance. Small increases that each pass add up until one PR crosses the
tolerance: check the benchmark's history before blaming that PR alone.

Take the numbers from the last CI run of the PR only:

```bash
gh run download <run-id> -p 'tally-*' -D tmp/tally
vendor/bin/tally accept tmp/tally
```

A diff on `#[Expected]` is a performance change: review it, never approve it on
autopilot.

## The storefront fixture

`performance/themes/storefront/` is a deliberately plausible storefront: real
`<head>` metadata, a main nav, breadcrumbs, a product grid, a filter sidebar, a
variant picker, a specs table, a multi-column footer. The nav is deliberately
flat — multi-level menus were ruled out as breadth the fixture does not need, and
depth comes from the product graph instead. It uses only tags a real theme
would use (`render`, `for`/`else`, `if`, `unless`, `case`, `capture`, `cycle`,
`assign`, `break`, `continue`, `{% liquid %}`).

Constraints that are not obvious from reading the code:

- **`Database` assigns, it does not compute.** No scans, no reductions, no
  sorting. Nothing is memoized either, so the page-local object graph is rebuilt
  on every render *inside the measured region* — any computation added here moves
  the theme numbers for reasons that have nothing to do with the library.
  Derived values go into the literal data or onto a drop method, where they are
  measured as template work. `StorefrontTheme::renderData()` builds page and
  layout data once, sharing only the `shop` drop where both contexts need it.
- **Fixed dataset: 24 products.** A deliberate page size, not an accident.
- **Fresh drops per render.** `#[Cache]` therefore starts cold on every render,
  and no state is shared between revolutions. Memoized instances would measure a
  warm cache 95% of the time and leak `ContextAware` state across revs.
- **Two render contexts per page.** The page renders into one, the layout into
  another, both from a single fixture setup. Consequence: nothing under `layout/`
  may read a variable a template assigned — it would render empty.
- **No missing lookups.** Every field the theme reads exists, so empty output in
  a benchmark is a bug rather than an expected state.
- **Template sources are read in `setUp`,** never inside a subject. Reading 29
  files per revolution measured the filesystem, not the tokenizer.

### Drop resolution strategies

`Drop::__get` resolves names through four branches of very different cost, so the
fixture assigns them on purpose rather than by accident — while keeping each one
somewhere a real storefront drop would genuinely use it:

| Strategy | Cost | Where the fixture uses it |
| --- | --- | --- |
| Public typed property | Cheapest — first lookup loop | Stored fields: `title`, `handle`, `price_cents` |
| Invokable method | Misses the property loop first | Derived values: `on_sale`, `saving_cents`, `url` |
| `#[Cache]`d method | Method cost, once per instance | `in_stock_variant_count` — walks every variant; read twice per instance on the product page, once on a card |
| `liquidMethodMissing` | Most expensive; a **miss** throws and catches up to three exceptions | `MetafieldsDrop` only, where keys are genuinely arbitrary |

## Verifying the fixture

Benchmarks run with the library defaults, because that is what an application
looks like. `tests/Integration/Performance/StorefrontThemeTest.php` instead
renders every page with `strictVariables`, `strictFilters` and `rethrowErrors`
all on, so a missing variable, a missing filter or a swallowed render error fails
the suite instead of quietly rendering as empty output.

There are deliberately **no snapshot assertions**. The theme is expected to keep
growing, and a snapshot over a fast-changing fixture gets regenerated on autopilot
until it asserts nothing. Strict mode cannot be silenced that way.

## Deferred

Known gaps, in rough priority order:

- **Additional per-tag `operations` subjects.** The suite isolates property,
  method, `liquidMethodMissing` hit/miss, filters and list-size scaling, but it
  does not yet isolate `case`, `capture`, `cycle` or `render` depth.
- **Full-theme size scaling.** `GrowthBench` checks 4 / 24 / 96-product loop
  scaling, while the storefront theme deliberately keeps a fixed 24-product
  dataset. A second full-theme size profile would be useful only if a suspected
  regression needs that wider lens.
- **Coverage-only tags.** `tablerow`, `increment`, `decrement`, `ifchanged`,
  `raw` and `doc` are unbenchmarked. Real themes barely use them, so they belong
  in `operations` rather than in the theme.
- **`TemplateCacheBench` shape.** Seven subjects are driven by seven near-identical
  `setUp*` wrappers around a string `match`; `ParamProviders` could reduce that
  repetition. Each benchmark setup now receives a unique temporary cache path,
  so concurrent runs do not share cache files.
