---
title: Define compiler extension and fallback seams
type: wayfinder:grilling
status: closed
assignee: Fabio Capucci
parent: ../map.md
blocks: 001-artifact-contract.md
---

## Question

What stable interface should custom nodes and tags implement to emit optimized PHP, and what generic runtime fallback should handle existing or third-party tags that do not opt into direct compilation?

## Resolution

`CanBeCompiled` is an optional interface implemented by individual nodes or tags; its compiler-context method emits optimized PHP without changing `Tag`, `LiquidExtension`, `TagRegistry`, or filter registration APIs. Filters continue to resolve through the runtime context. Nodes and tags without the interface use their existing `render()` or `stream()` behavior through a compiler fallback. If a fallback node cannot be safely reconstructed in the artifact, compilation declines that optimized path and the caller retains the interpreted template.
