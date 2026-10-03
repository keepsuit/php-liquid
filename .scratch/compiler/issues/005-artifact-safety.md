---
title: Define compiled artifact safety and deployment behavior
type: wayfinder:grilling
status: closed
assignee: Fabio Capucci
parent: ../map.md
blocks: 001-artifact-contract.md
---

## Question

What guarantees are required when writing and loading generated PHP files—safe source emission, path ownership, atomic replacement, corrupted artifacts, concurrent writers, and OPcache/deployment behavior?

## Resolution

The compiled artifact directory is trusted and application-owned; generated PHP is not sandboxed. Every template-originated string, name, and value must pass through Symfony VarExporter, and template content must never reach raw PHP emission or choose generated identifiers. Raw source-generation hooks are trusted compiler/plugin code, not template input. Compilation must fail clearly when a value or fallback node cannot be safely encoded or reconstructed. Generated class identities are deterministic from template/source content and do not include a compiler-version marker; the application owns invalidation.

Artifacts are written to a same-directory temporary file and atomically published, with deterministic content-based artifact/class identities. Loading validates the returned `TemplateInterface` object and treats corrupt or invalid files as cache misses. OPcache is invalidated after publication; deployments may use versioned or rebuilt artifact directories. Security coverage must include PHP-looking template payloads, quotes, escapes, control characters, and generated-source syntax validation. Existing interpreted caches remain unchanged.
