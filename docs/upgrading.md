# Upgrading dibi/dibi

## 5.1.1

- **Removed** `Dibi\Bridges\Nette\DibiExtension22` (replaced since 5.0.1): register `Dibi\Bridges\Nette\DibiExtension3` in the `extensions` section of the NEON configuration instead (dfacaae0)
- **Signature** `Dibi\Connection::$onEvent`: the property type is now `array` and no longer nullable; assign `[]` instead of `null` to disable events (064c1bef)
- **Signature** `Dibi\Reflection\Table::getPrimaryKey()`: the return type is now nullable and the method returns `null` for a table without a primary key; callers must handle `null` (064c1bef)
- **Signature** native parameter and return types were added to further members; the ones users call or override are `Dibi\Connection::onEvent()` (now requires a `Dibi\Event`), `Dibi\Connection::__unserialize()`, and `$code` of `Dibi\Drivers\OracleDriver::createException()`, `Dibi\Drivers\PostgreDriver::createException()` and `Dibi\Drivers\SqliteDriver::createException()` (now `int|string`); affects subclasses overriding them (c168d8a5)
- **Changed** `Dibi\Reflection\Column::getFullName()` now falls back to the column name; previously it could return `null` despite the declared `string` return type (064c1bef)

## 5.1.0

- **Replaced** `Dibi\Connection::__sleep()` with `Dibi\Connection::__serialize()`: a subclass customizing serialization must override `Dibi\Connection::__serialize()`, which returns an array (658dbe38)
- **Replaced** `Dibi\Connection::__wakeup()` with `Dibi\Connection::__unserialize()`: a subclass customizing deserialization must override `Dibi\Connection::__unserialize()`, which receives the data array (658dbe38)
- **Signature** `$version` of `Dibi\Drivers\PostgreReflector::__construct()`: the parameter was removed together with support for PostgreSQL older than 9.3; a caller passing it must drop the second argument (32b69762)
- **Signature** `$filter` of `Dibi\Loggers\FileLogger::__construct()`: no longer accepts `null` and defaults to `Dibi\Event::QUERY`; pass an explicit `Dibi\Event` flag instead of `null` (ce1ba466)
- **Signature** `$filter` of `Dibi\Bridges\Tracy\Panel::__construct()`: no longer accepts `null` and defaults to `Dibi\Event::QUERY`; pass an explicit `Dibi\Event` flag instead of `null` (ce1ba466)
- **Signature** `Dibi\Event::$connection`: the property is now `readonly`; assigning to it after construction throws `Error` (f484630e)
- **Signature** `Dibi\Event::$result`: the property is now `readonly`; assigning to it after construction throws `Error` (f484630e)
- **Signature** `Dibi\Event::$sql`: the property is now `readonly`; assigning to it after construction throws `Error` (f484630e)

## 5.0.2

- **Renamed** `Dibi\Type::TEXT` → `Dibi\Type::Text`; the old name is silently deprecated (d707b4ba)
- **Renamed** `Dibi\Type::BINARY` → `Dibi\Type::Binary`; the old name is silently deprecated (d707b4ba)
- **Renamed** `Dibi\Type::BOOL` → `Dibi\Type::Bool`; the old name is silently deprecated (d707b4ba)
- **Renamed** `Dibi\Type::INTEGER` → `Dibi\Type::Integer`; the old name is silently deprecated (d707b4ba)
- **Renamed** `Dibi\Type::FLOAT` → `Dibi\Type::Float`; the old name is silently deprecated (d707b4ba)
- **Renamed** `Dibi\Type::DATE` → `Dibi\Type::Date`; the old name is silently deprecated (d707b4ba)
- **Renamed** `Dibi\Type::DATETIME` → `Dibi\Type::DateTime`; the old name is silently deprecated (d707b4ba)
- **Renamed** `Dibi\Type::TIME` → `Dibi\Type::Time`; the old name is silently deprecated (d707b4ba)
- **Renamed** `Dibi\Type::TIME_INTERVAL` → `Dibi\Type::TimeInterval`; the old name is silently deprecated (d707b4ba)
- **Renamed** `Dibi\Fluent::REMOVE` → `Dibi\Fluent::Remove`; the old name is silently deprecated (d707b4ba)
- **Renamed** `Dibi\Drivers\MySqliDriver::ERROR_ACCESS_DENIED` → `Dibi\Drivers\MySqliDriver::ErrorAccessDenied`; the old name is silently deprecated (d707b4ba)
- **Renamed** `Dibi\Drivers\MySqliDriver::ERROR_DATA_TRUNCATED` → `Dibi\Drivers\MySqliDriver::ErrorDataTruncated`; the old name is silently deprecated (d707b4ba)
- **Renamed** `Dibi\Drivers\MySqliDriver::ERROR_DUPLICATE_ENTRY` → `Dibi\Drivers\MySqliDriver::ErrorDuplicateEntry`; the old name is silently deprecated (d707b4ba)
- **Renamed** `Dibi\Drivers\FirebirdDriver::ERROR_EXCEPTION_THROWN` → `Dibi\Drivers\FirebirdDriver::ErrorExceptionThrown`; the old name is silently deprecated (d707b4ba)
- **Renamed** `dibi::VERSION` → `dibi::Version`; the old name is silently deprecated (d707b4ba)
- **Renamed** `dibi::AFFECTED_ROWS` → `Dibi\Fluent::AffectedRows`; the constant moved to another class and the old name is silently deprecated (d707b4ba)
- **Renamed** `dibi::IDENTIFIER` → `Dibi\Fluent::Identifier`; the constant moved to another class and the old name is silently deprecated (d707b4ba)
- **Changed** `Dibi\Drivers\PdoDriver::escapeText()` now escapes text for the `sqlsrv` PDO driver as an `N'...'` national-character literal; previously the value went through PDO quoting, so the generated SQL differs (29b58d64)
- **Changed** `Dibi\Drivers\PdoDriver::escapeBinary()` now escapes binary values for the `sqlsrv` PDO driver as a `0x` hex literal; previously the value went through PDO quoting, so the generated SQL differs (29b58d64)
- **Changed** `Dibi\Drivers\SqliteDriver` again lets the native `SQLite3Exception` propagate on PHP 8.3+, where SQLite3 exceptions are enabled by default; this reverts the wrapping into `Dibi\DriverException` released in 5.0.1 (23f65ef8)

## 5.0.1

- **Replaced** `Dibi\Bridges\Nette\DibiExtension22` with `Dibi\Bridges\Nette\DibiExtension3`: register the new class in the `extensions` section of the NEON configuration; it validates its configuration through a schema, while the old class keeps working (68002674)
- **Changed** `Dibi\Drivers\SqliteDriver` turns SQLite3 errors into `Dibi\DriverException` again on PHP 8.3+, where SQLite3 exceptions are enabled by default and previously propagated as a native `SQLite3Exception`; reverted in 5.0.2 (bb1f7d4b)

## 5.0.0

- **Removed** `Dibi\Strict`: the trait is gone and no class uses it any more; calling an undefined method or reading an undeclared property on a Dibi object no longer throws `LogicException` with a suggestion but behaves as plain PHP (b01d97ac)
- **Signature** `Dibi\Driver::getResource()`: the method now declares the return type `mixed`; affects custom implementations of the interface, which must declare it too (08dfc374)
- **Signature** `Dibi\ResultDriver::getResultResource()`: the method now declares the return type `mixed`; affects custom implementations of the interface, which must declare it too (08dfc374)
- **Signature** `$message` of `Dibi\PcreException::__construct()`: the parameter was removed; the message and code are now derived internally from the last PCRE error (82575326)
- **Signature** native parameter, return and property types were added across the public API and fluent methods return `static` instead of `self`; the ones users override or assign to are `Dibi\Fluent::__call()`, `Dibi\Fluent::clause()`, `Dibi\Fluent::setFlag()`, `Dibi\Result::setType()`, `Dibi\Result::setRowClass()`, `Dibi\DataSource::select()` and `Dibi\DataSource::orderBy()` (the column parameter is now `string|array`), `Dibi\Event::done()`, `Dibi\Event::$count`, `Dibi\Event::$source`, `dibi::$sql` and `dibi::$elapsedTime` (08dfc374, 7acef0c3)
- **Changed** `dibi::$totalTime` is now a non-nullable `float` starting at `0.0`; previously it was untyped and `null` until the first query, so a `=== null` test no longer works (7acef0c3)
- **Changed** `Dibi\DataSource::__toString()` now lets an exception from SQL generation propagate; previously the failure was reported with `E_USER_ERROR` and the method returned an empty string (8915b034)
- **Changed** `Dibi\Fluent::__toString()` now lets an exception from SQL generation propagate; previously the failure was reported with `E_USER_ERROR` and the method returned an empty string (8915b034)

## 4.2.6

- **Signature** `$reflector` of `Dibi\Reflection\Column::__construct()`: the parameter lost its default value and must always be passed (82150d12)

## 4.2.5

- **Changed** `Dibi\Result` normalizes a `Dibi\Type::DATE`, `Dibi\Type::DATETIME` or `Dibi\Type::TIME` column to `null` only for a genuine zero date; previously every value whose first three characters were `000`, such as `0001-01-01`, became `null` and now it is returned as `Dibi\DateTime` (2ac618ff)
- **Changed** `Dibi\Row::asDateTime()` returns a `Dibi\DateTime` for a value such as `0001-01-01`; previously every value whose first three characters were `000` was treated as a zero date and returned `null` (2ac618ff)
- **Changed** `Dibi\Helpers::detectType()` detects PostgreSQL range types such as `int4range` as `Dibi\Type::TEXT`; previously they matched the integer pattern and their values were normalized to `int` (marked @internal) (cb82357c)
- **Config** `dibi.profiler`: with the key unset the profiler is no longer enabled automatically in CLI mode; set `dibi.profiler: true` to force it in a console script (1881fea0)

## 4.2.2

- **Changed** `Dibi\Connection::begin()` throws `LogicException` when called inside a `Dibi\Connection::transaction()` callback; previously it was allowed (3066fea2)
- **Changed** `Dibi\Connection::commit()` throws `LogicException` when called inside a `Dibi\Connection::transaction()` callback; previously it was allowed (3066fea2)
- **Changed** `Dibi\Connection::rollback()` throws `LogicException` when called inside a `Dibi\Connection::transaction()` callback; previously it was allowed (3066fea2)
- **Changed** `Dibi\Connection::transaction()` shares one database transaction across nested calls and opens and commits it only at the outermost one; previously every call issued its own begin and commit, so an exception in a nested call rolled back only that call instead of the whole outer transaction (b00e5562)
- **Changed** `Dibi\Connection::transaction()` invokes the callback with the `Dibi\Connection` as its first argument; previously the callback was invoked with no arguments (7049949b)

## 4.2.1

- **Changed** `Dibi\Drivers\PostgreReflector::__construct()` no longer throws `Dibi\DriverException` for a PostgreSQL server older than 7.4; construction now always succeeds regardless of the reported version (1a770482)

## 4.2.0

- **Removed** `Dibi\Connection::translateArgs()`: the protected method was the only hook a subclass had into the translation of a query, and none replaces it, since `Dibi\Connection::translate()`, `Dibi\Connection::query()` and `Dibi\Connection::nativeQuery()` are final; rewrite identifiers through `Dibi\Connection::getSubstitutes()`, or watch the queries through the handlers of `Dibi\Connection::$onEvent` (decb30de)
- **Config** `dibi.result.formatJson`: the value `string` was renamed to `native`; `string` still works (c4649602)

## 4.1.4

- **Changed** `Dibi\Drivers\SqlsrvDriver::escapeBinary()` produces a `0x` hex literal; previously it produced a quoted string literal, so the SQL generated for the `%bin` modifier differs (0ee46287)

## 4.1.3

- **Changed** `Dibi\Result` throws `RuntimeException` during normalization when a column type is neither `null` nor a known `Dibi\Type` constant; previously the raw value was returned unconverted; affects custom types set with `Dibi\Result::setType()` (0f69d5d3)

## 4.1.2

- **Changed** `Dibi\Driver::escapeLike()` reads `$pos` as bit flags (1 adds a leading `%`, 2 a trailing one) instead of comparing its sign, so `$pos` of `0` now means an exact match instead of a match on both sides; affects custom driver implementations and direct callers (294787a2)

## 4.1.0

- **Removed** `Dibi\Strict::extensionMethod()`: extension methods are no longer supported, there is no replacement (e66cb84c)
- **Renamed** `Dibi\Connection::affectedRows()` → `Dibi\Connection::getAffectedRows()`; the old name was removed (e66cb84c)
- **Renamed** `Dibi\Connection::insertId()` → `Dibi\Connection::getInsertId()`; the old name was removed (e66cb84c)
- **Renamed** `dibi::affectedRows()` → `dibi::getAffectedRows()`; the old name was removed (e66cb84c)
- **Renamed** `dibi::insertId()` → `dibi::getInsertId()`; the old name was removed (e66cb84c)
- **Replaced** `Dibi\DateTime::modifyClone()` with `Dibi\DateTime::modify()`: the old name was removed and `Dibi\DateTime::modify()` requires a non-empty modifier, whereas `modifyClone()` without an argument returned a plain clone (e66cb84c)
- **Signature** `$config` of `Dibi\Connection::__construct()`: only `array` is accepted; support for a connection string and for a `Traversable` was removed and any other type now fails with `TypeError` (e66cb84c)
- **Signature** `Dibi\Driver::escapeDateInterval()`: the method was added to the interface; a custom driver implementation must add it (369768a6)
- **Signature** `$value` of `Dibi\Driver::escapeDate()`: only `DateTimeInterface` is accepted and the return type is `string`; a string or an int passed directly to the driver now fails, although the translator still converts the values of the `%d` and `%t` modifiers itself (78d6603b)
- **Signature** `$value` of `Dibi\Driver::escapeDateTime()`: only `DateTimeInterface` is accepted and the return type is `string`; a string or an int passed directly to the driver now fails, although the translator still converts the values of the `%d` and `%t` modifiers itself (78d6603b)
- **Changed** `Dibi\Connection::getInsertId()` returns a negative ID as a valid result and throws `Dibi\Exception` only when the driver returns `null`; previously any ID lower than 1 threw (c1640c5e)
- **Changed** `Dibi\Drivers\SqlsrvDriver::escapeText()` produces an `N'...'` national-character literal; previously it produced a plain quoted string, so the generated SQL differs (7f222793)
