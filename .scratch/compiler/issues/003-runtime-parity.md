---
title: Define compiled render and stream parity
type: wayfinder:grilling
status: closed
assignee: Fabio Capucci
parent: ../map.md
blocks: 001-artifact-contract.md
---

## Question

Which observable behaviors must compiled render and stream preserve—output chunking, lazy execution, state and outputs, resource limits, and Liquid exception metadata—and how should the generated artifact expose those semantics?

## Resolution

Compiled artifacts must preserve the existing `Template` contract for both `render()` and `stream()`. Streaming remains lazy and preserves the interpreter's observable chunk boundaries; it must not collapse output into one final chunk. Compiled execution must merge and persist shared outputs and errors, enforce the same render/assign/resource limits, preserve interrupt behavior, and attach the same template and line metadata to Liquid exceptions. A compiled path that cannot preserve these semantics falls back to the existing interpreter behavior for that operation.
