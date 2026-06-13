# PHP Liquid

Language for this PHP port of the Shopify Liquid template engine.

## Compatibility

**Strict Parsing**:
The PHP Liquid parsing contract that rejects malformed template syntax during parsing. This is the canonical term for the port's default parser behavior.
_Avoid_: error modes, rigid mode, strict2 mode

**Cumulative Resource Limits**:
Resource budgets that count render and assignment work across a full render tree, including partial templates. They complement per-render limits rather than replacing them.
_Avoid_: global limits, partial limits

**Squish**:
A standard Liquid filter that normalizes whitespace by trimming surrounding whitespace and collapsing each internal whitespace run to a single space.
_Avoid_: compact whitespace, normalize whitespace

**Static Partial Render**:
A render operation whose partial name is fixed by the template source unless an extension explicitly opts into dynamic partials.
_Avoid_: dynamic render, include

**Parser Hardening**:
Compatibility work that makes malformed Liquid syntax fail during parsing with deterministic syntax errors. It is part of strict parsing, not a separate parser mode.
_Avoid_: rigid parsing, strict2 parsing
