# Fluent & the static facade

Two coupling points that surprise: Fluent's configuration is process-global, and
`Connection` writes into the `dibi` static facade.

## Fluent: process-global config, `__call`, and reference-based `$cursor`

`Fluent`'s clause configuration lives in **four `public static array`s** —
`$masks`, `$modifiers`, `$separators`, `$clauseSwitches` (plus a shared static
`$normalizer` `HashMap`). Being `public static`, they are **shared by every connection /
Fluent instance in the process and mutable from anywhere** — changing `Fluent::$masks`
in one place affects all queries everywhere. Adding a clause (e.g. `WITH`, `RETURNING`)
means editing these static arrays.

The whole builder API is **`__call` + `@method` phpDoc** — there are no real per-clause
methods. The active clause is tracked by `private $cursor` bound **by reference** to the
current clause array (`$this->cursor = &$this->clauses[$clause]`), which is why it can't be
typed. The consequence for editing: **`__clone()` must manually rebind every reference**
(it re-`&`s each clause and resets the cursor via a fragile `&$foo` idiom), or a clone
would share clause arrays with the original.

## `Connection` → static facade coupling

A library class should not depend on a global facade, but `Connection` does: it writes
`\dibi::$sql = $sql;` on every native query, and `Event` updates further facade statics
(`\dibi::$elapsedTime`, `$totalTime`, and `$numOfQueries++`) in its constructor and
`done()`.

**Two nuances that bite:**

- **`$numOfQueries` counts non-queries too.** The increment is unconditional in the
  `Event` constructor, and an `Event` is created for `CONNECT`, `BEGIN`, `COMMIT`, and
  `ROLLBACK` as well as `QUERY` — so the counter measures *all profiled events*, not just
  SQL queries.
- **But only when a listener is registered.** Each event is `$this->onEvent ? new Event(…)
  : null`, so with no `$onEvent` listener the counter stays 0 and the facade statics are
  not updated by events (only the raw `\dibi::$sql` write in `nativeQuery` still happens).
  The statistics are therefore a profiling artifact, present only under an active listener.
