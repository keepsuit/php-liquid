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

Compiled artifacts must preserve the `TemplateInterface` contract for both `render()` and `stream()`. Streaming remains lazy and must produce the same complete output; chunk boundaries may differ from the interpreter. Compiled `render()` collects the compiled stream, while the standard `Template` retains separate render and stream implementations. Compiled execution must merge and persist shared outputs and errors, enforce the same render/assign/resource limits, preserve interrupt behavior, and attach the same template and source-line metadata to Liquid exceptions. A compiled path that cannot preserve these semantics uses a safe interpreter fallback for the affected node or fails compilation when that fallback cannot be reconstructed.
