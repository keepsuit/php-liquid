---
title: Define the public compiler artifact contract
type: wayfinder:grilling
status: closed
assignee: Fabio Capucci
parent: ../map.md
blocks: []
---

## Question

What public compile API should write the PHP artifact, and what exactly should requiring that file return or define so callers can render it with the existing RenderContext contract?

## Resolution

`Environment::compile(Template $template, string $compiledPath)` is the additive public entry point. It writes a PHP artifact at the caller-provided path; requiring that artifact returns a `Template`-compatible compiled object that can render with the existing `RenderContext`. Existing parsing, rendering, and interpreted cache APIs remain unchanged. Cache identity and environment consistency remain application-managed.
