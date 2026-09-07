---
title: Define partial graph compilation and invalidation
type: wayfinder:grilling
status: closed
assignee: Fabio Capucci
parent: ../map.md
blocks: 001-artifact-contract.md
---

## Question

For templates that load partials through render/include tags, should compilation produce one artifact per template or a root artifact for the reachable graph, and what application-managed cache key and invalidation contract keeps the graph coherent?

## Resolution

Version one produces one PHP artifact per logical template. The compiled template keeps partial loading as a runtime lookup by template name, so the application can compile partials independently and replace only the artifact whose source changed. Static partial names discovered during parsing may drive precompilation or application-level dependency tracking; dynamic partials retain the existing runtime path. The compiler does not define cache keys or invalidation rules. Static partial inlining is deferred to [Evaluate static partial inlining](007-static-partial-inlining.md).
