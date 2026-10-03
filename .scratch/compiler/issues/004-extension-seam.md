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

`CanBeCompiled` is an optional trusted PHP extension interface implemented by individual nodes or tags; its fluent compiler-context method emits the stream-oriented PHP body without changing `Tag`, `LiquidExtension`, `TagRegistry`, or filter registration APIs. Filters continue to resolve through the runtime context. Nodes and tags without the interface use their existing `stream()` or `render()` behavior through a fallback that is reconstructed with Symfony VarExporter and loaded once per artifact. Template-controlled text, names, and values never reach raw PHP emission. If a fallback node cannot be safely represented by VarExporter, compilation fails with the template name, node class, and source line.
