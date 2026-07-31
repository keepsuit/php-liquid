# Compiler Wayfinder

## Destination

Produce an implementation-ready, benchmark-backed design for an additive PHP compiler path in php-liquid: the existing interpreter remains unchanged; an explicit compile operation writes a PHP artifact that can be required and rendered; the design settles compiler interfaces, tag/node coverage, partial dependencies, runtime semantics, artifact handling, performance gates, and rollout.

## Notes

- Domain: php-liquid template compilation and compiled-template caching.
- Consult grilling, domain-modeling, research, and the existing benchmark conventions as tickets require.
- Planning only until the map is complete; implementation follows as a separate handoff.
- Compatibility is the default preference, not an absolute constraint.
- Existing tags remain supported; nodes/tags opt into direct compilation through an interface, with runtime fallback for non-compilable cases.
- The application owns environment consistency and invalidation, following the existing template-cache operational model.
- The generated artifact should be a PHP file that can be required and rendered; current interpreted behavior and current cache implementations are not changed by this effort.

## Decisions so far

- [Define the public compiler artifact contract](issues/001-artifact-contract.md) — Explicit compilation writes a caller-selected PHP artifact, and `require` returns a `Template`-compatible renderable object; existing APIs stay unchanged.
- [Define partial graph compilation and invalidation](issues/002-partial-graph.md) — Version one uses one artifact per logical template and runtime partial lookup; applications own precompilation and invalidation.
- [Define compiled render and stream parity](issues/003-runtime-parity.md) — Compiled execution preserves lazy chunked streams, state, limits, interrupts, and exception metadata, with interpreter fallback where needed.
- [Define compiler extension and fallback seams](issues/004-extension-seam.md) — Nodes and tags opt into direct PHP generation through `CanBeCompiled`; existing registrations and runtime fallbacks remain valid.
- [Define compiled artifact safety and deployment behavior](issues/005-artifact-safety.md) — Template literals are encoded as data, artifacts are trusted and atomically published, and invalid files fail closed as cache misses.
- [Define compiler performance and rollout gates](issues/006-performance-gate.md) — A main-baselined macro workload separates compile/load/render/stream costs, requires improvement beyond noise, and keeps rollout opt-in.
- [Evaluate static partial inlining](issues/007-static-partial-inlining.md) — Static, acyclic, fully compilable partial graphs may be inlined later with preserved partial context and stream semantics and transitive dependency hashes; runtime lookup remains the default until benchmark gates pass.

## Not yet specified

- Generated PHP line-to-Liquid debug maps beyond preserving Liquid source lines in runtime exceptions.

## Out of scope

- Making compilation the default execution path.
- Replacing or redesigning the existing interpreted template caches.
- Removing support for tags or requiring every existing tag to be rewritten before compilation can be used.
