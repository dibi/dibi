# Drivers & dialects

Each engine has its own driver; dialect differences are handled by **per-driver
duplication**, which is where the traps live.

## Constraint-exception mapping is asymmetric

A driver's `static createException()` maps a native error into a typed
`UniqueConstraintViolationException` / `ForeignKeyConstraintViolationException` /
`NotNullConstraintViolationException`. **Only some drivers do this**, so a
`catch (UniqueConstraintViolationException …)` silently degrades to a bare
`DriverException` when you switch to a driver that doesn't map:

- **Maps:** the four native drivers `MySqli` (numeric codes), `Postgre` (SQLSTATE),
  `Oracle`, `Sqlite` — plus `Sqlite3` by inheritance (five in effect).
- **`PdoDriver` also maps** — it delegates by `$driverName` (`match`) to
  `MySqli`/`Oracle`/`Postgre`/`Sqlite::createException`, so PDO+mysql/oci/pgsql/sqlite are
  covered. (A common misreading is that PdoDriver lacks mapping; it doesn't.)
- **Does NOT map** (throws a bare `DriverException`): `Odbc`, `Sqlsrv`, `Firebird`, and
  `PdoDriver` when `$driverName` is `odbc`/`sqlsrv`/`mssql`/`dblib`.

A subtle inconsistency: the PDO-pgsql path passes the **SQLSTATE string** while the other
paths pass the **integer code**, so the code space seen by a handler differs by path.

## `applyLimit` is duplicated across eight files

`applyLimit(string &$sql, ?int $limit, ?int $offset)` is copy-pasted per driver with the
dialect's own clause (`LIMIT … OFFSET`, `OFFSET … ROWS FETCH NEXT`, `SELECT TOP`, `ROWNUM`,
`FIRST … SKIP`, …) and a near-identical negative-value guard
(`if ($limit < 0 || $offset < 0) throw NotSupportedException`). `PdoDriver` re-implements
all of this internally by **branching on `$driverName`** (`switch`), duplicating the other
drivers' logic — functional, but the maintenance seam to know about when touching any
LIMIT/OFFSET behavior.
