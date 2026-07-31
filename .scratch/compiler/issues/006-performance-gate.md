---
title: Define compiler performance and rollout gates
type: wayfinder:grilling
status: closed
assignee: Fabio Capucci
parent: ../map.md
blocks: 001-artifact-contract.md
---

## Question

Which representative workloads and separate tokenize, parse, compile, load, render, and stream measurements prove that compiled templates improve the target path without regressing correctness, memory, or normal interpreter performance?

## Resolution

The gate uses an identical baseline on `main` and one deterministic production-shaped storefront workload. It measures tokenize, parse, compile/write, fresh artifact require/load, compiled render, compiled stream, interpreted render/stream, and existing template-cache load/render separately. Correctness checks compare exact rendered output and stream chunks outside timed subjects. A compiled path must improve beyond the existing ±2% noise band with RSD at or below 5%, avoid interpreter regressions and material memory growth, and report compile/write cost separately. Rollout remains opt-in; static partial inlining is evaluated only after this baseline is reliable.
