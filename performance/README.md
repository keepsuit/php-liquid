# Benchmark suite

```bash
composer benchmark             # default group: the storefront theme
composer benchmark:cache       # cache group: template-cache backends
composer benchmark:operations  # operations group: individual operations
php performance/profile-theme.php --output=profile.json
```

## What each group is for

The three groups have different jobs, and conflating them is how a benchmark suite
stops being useful.

**`default`** (`ThemeBench`) renders the storefront theme — 29 templates across
four pages. It answers *"did rendering get slower"* and nothing more. It cannot
tell you *what* got slower, because a regression in any one tag is averaged
across everything else. Don't expect it to localize.

**`cache`** (`TemplateCacheBench`) measures compilation and fresh-environment
loading for every supported template-cache backend.

**`operations`** (`OperationBench`) measures single operations on tiny templates.
This is where per-feature sensitivity lives, and where a benchmark is allowed to
be unrealistic: an artificial template that does one thing 64 times is a better
instrument than a realistic page.

The split is what lets the theme be realistic. Whenever realism and measurement
sensitivity conflict inside the theme, realism wins — sensitivity is not the
theme's job.

Pull-request comparisons enforce a worst-subject throughput regression threshold
of 5%. The comparator still labels changes inside its 2% noise band as neutral,
and marks RSD above 5% as high variance for review.

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
- **Full-theme size scaling.** The operations group exposes 4 / 24 / 96-product
  loop scaling, while the storefront theme deliberately keeps a fixed 24-product
  dataset. A second full-theme size profile would be useful only if a suspected
  regression needs that wider lens.
- **Coverage-only tags.** `tablerow`, `increment`, `decrement`, `ifchanged`,
  `raw` and `doc` are unbenchmarked. Real themes barely use them, so they belong
  in `operations` rather than in the theme.
- **`TemplateCacheBench` shape.** Six subjects are driven by six near-identical
  `setUp*` wrappers around a string `match`; `ParamProviders` could reduce that
  repetition. Each benchmark setup now receives a unique temporary cache path,
  so concurrent runs do not share cache files.
