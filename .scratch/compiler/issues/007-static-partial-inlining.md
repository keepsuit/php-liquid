---
title: Evaluate static partial inlining
type: wayfinder:grilling
status: closed
assignee: Fabio Capucci
parent: ../map.md
blocks: 003-runtime-parity.md, 006-performance-gate.md
---

## Question

When static partial dependencies are known at compile time, under what measured performance and semantic conditions should the compiler inline them into a parent artifact, and how would that affect invalidation, errors, streaming, and deployment?

## Resolution

Static partial inlining is a future opt-in optimization; version one keeps one artifact per logical template with runtime partial lookup.

A partial is eligible only when its name is a literal known during parsing, its complete transitive dependency graph is available and acyclic, and every participating node and tag can emit safe compiled code. Dynamic or unknown names, cycles, unsupported compilation, unsafe fallback, or incomplete dependency discovery retain runtime linking.

Inlining embeds the compiled partial body in the parent artifact but retains the partial's isolated `RenderContext` boundary; it must preserve complete render and stream output, output bags, template and line exception metadata, resource limits, interrupts, and current error handling. Chunk boundaries need not remain identical.

The parent artifact identity includes transitive dependency content hashes. The compiler does not impose a compiler-version component on generated class names; the application owns invalidation and may include its own artifact-format key. It must rebuild affected parents, publish a consistent artifact set atomically or through a versioned artifact directory, and never activate a parent with stale inlined dependencies.

Inlining is accepted only when exact output, error, and stream tests pass and the representative storefront benchmark improves compiled render and stream beyond the established noise band (more than 2%, RSD at most 5%) without interpreter regressions or material memory growth. If it does not clear that gate, runtime-linked artifacts remain the implementation.
