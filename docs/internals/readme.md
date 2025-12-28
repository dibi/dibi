# Dibi internals

How Dibi works underneath, for agents editing it. A database abstraction with several
non-trivial, cross-file mechanisms and a set of counterintuitive traps. The subsystems
don't share much context, so this is split by seam:

- **[translator.md](translator.md)** — the `%modifier` SQL builder and its unusual
  in-band error reporting.
- **[transactions.md](transactions.md)** — the nested-`transaction()` no-op and the
  partial-commit footgun.
- **[drivers.md](drivers.md)** — per-dialect duplication and the constraint-exception
  mapping asymmetry.
- **[result.md](result.md)** — DB→PHP type normalization and its edge cases.
- **[fluent-and-facade.md](fluent-and-facade.md)** — Fluent's process-global config and
  the `Connection` → static-facade coupling.
