# To My Agents!

It is my fervent wish that this file guide every AI coding agent working with code in this repository.

## Documentation

Any distilled, agent-facing documentation for this package - how it works
internally and the rationale behind key design decisions - lives in `docs/`.
Consult it before non-trivial changes; it is the source of truth from which the
public manual is distilled.

Dibi has several non-trivial, cross-file mechanisms with counterintuitive traps
(the `%modifier` translator, nested transactions, per-driver duplication, result
type normalization, Fluent's global config). Read the relevant `docs/internals/`
seam before editing one.

`docs/upgrading.md` records, per released version, every change visible to
users of the package (renames, removals, deprecations, signatures, behavior,
configuration), each with its commit hash; internal changes are not in it. It
is the place to look up the history of a public symbol. Do not write to it; it
is extended at release time from the git history.

## Project Overview

**Dibi** is a standalone PHP database abstraction library (DBAL) with a unified
interface over MySQL/MariaDB, PostgreSQL, SQLite, MS SQL, Oracle, Firebird, and
ODBC (native or PDO drivers). Zero production dependencies.

- **PHP Version**: 8.2 - 8.6
- **Package**: `dibi/dibi` (author: David Grudl)

## Essential Commands

```bash
# Run all tests
vendor/bin/tester tests -s        # or: composer tester
vendor/bin/tester tests/dibi/DataSource.phpt -s

# Static analysis (PHPStan level 5)
composer phpstan
```

Many tests hit real databases via `tests/databases.ini`. To test against
MySQL/PostgreSQL without local installs:

```bash
docker compose up -d
cp tests/databases.docker.ini tests/databases.ini
vendor/bin/tester tests -s
docker compose down
```

## Conventions

- Every file starts with `declare(strict_types=1);`; **tabs**; single quotes; two
  blank lines between methods; no `Abstract`/`Interface`/`I` prefixes; Nette Coding
  Standard.
- Tests are Nette Tester `.phpt` under `tests/dibi/`; SQL fixtures in
  `tests/dibi/data/`; use `Assert::same`/`match`/`exception`.

## Working in this repo

- **The `Translator` is the most complex piece, and its error reporting is in-band.**
  `formatValue` returns a `**text**`-wrapped marker **and** pushes to an `$errors`
  array; `translate()` throws when `$errors` is non-empty. The `**` is just a stripped
  delimiter, **not** the decision signal - don't switch to scanning the SQL for `**`.
  `%sN`/`%iN` turn `=== 0`/`''` into `NULL` (but not `'0'` or `0.0`).
- **Nested `transaction()` is a counter no-op with NO savepoints.** The drivers have a
  savepoint API but `transaction()` doesn't use it, so an inner failure can leave an
  outer partial commit - a real footgun. Manual `begin/commit/rollback` inside a
  `transaction()` callback is forbidden.
- **Per-driver duplication is real:** `applyLimit` is copied across ~8 drivers and
  `PdoDriver` branches on `$driverName`. **Constraint-exception mapping is asymmetric**
  - MySqli/Postgre/Oracle/Sqlite and their PDO variants map to typed exceptions;
  ODBC/Sqlsrv/Firebird/PDO-odbc/sqlsrv/mssql do **not**.
- **`Result::normalize` is a long `elseif` over `Type` string constants;** `TIME(3)`
  drops fractional seconds and `0000-00-...` becomes `null` - edge cases to preserve.
- **`Fluent` has four process-global static config fields** mutable from anywhere, and
  its `$cursor` is by-reference so `__clone` must rebind it manually. `Connection`
  couples to the `\dibi::` static facade.
- **`numOfQueries` counts CONNECT/BEGIN/COMMIT/etc. only when an event listener is
  registered** (`$this->onEvent ? new Event : null`); otherwise it stays 0.
- User-facing how-to (the `%modifier` catalog, Fluent API, `fetchAssoc` descriptors,
  transactions, substitutions, type handling) is manual material and lives on the web.
