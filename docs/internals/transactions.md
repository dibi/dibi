# Transactions

Nesting is **counter-only**, with **no savepoints** — and that is a real footgun.

`transaction(callable)` tracks `$transactionDepth`: a real `BEGIN` is issued only on the
`0→1` transition, a real `COMMIT` only on `1→0`, and `ROLLBACK` only when an exception
unwinds back to depth 0. A **nested** `transaction()` call merely increments the counter
and issues **no SQL** — `begin()`/`commit()`/`rollback()` are always called **without a
savepoint argument**.

**The trap:** the driver layer *does* implement savepoints (`begin(?string $savepoint)`
emits `SAVEPOINT …` in MySQL/Postgres/SQLite/…), but `transaction()` never passes one.
So if an inner `transaction()` fails and the outer callback **catches the exception and
continues**, there is no partial rollback — the whole transaction commits together at
depth 0, persisting a partially-applied state. (Fixing this by auto-creating a savepoint
at depth > 0 is the top item on the wishlist.)

Manual `begin()`/`commit()`/`rollback()` (which *can* take a savepoint name) are
**forbidden inside a `transaction()` callback** — each throws a `LogicException` when
`$transactionDepth !== 0`, so the counter can't be desynchronized. Savepoints are
therefore only reachable by driving `begin/commit/rollback` yourself, outside
`transaction()`.
