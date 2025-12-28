# Result & type normalization

Every fetched row is converted DB→PHP in `Result::normalize(array &$row)`, called from
each `fetch()`/`fetchSingle()`. Column types are detected up front (`detectTypes()`, from
the native type via the type cache) as **string constants `Type::*`**, and normalization
is a long `elseif` chain on that type. `native` format (and a `null` type) bypass
conversion entirely.

The behaviors that surprise:

- **`Type::Integer`** guards against overflow — if `$value * 1` would become a float, the
  original **string is kept** rather than lose precision. `Type::Float` similarly hand-trims
  zeros/sign and keeps the string for non-representable values.
- **`Type::Bool`** is `((bool) $value) && $value !== 'f' && $value !== 'F'` (handles
  PostgreSQL's `'f'`).
- **Datetime/date/time**: `'0000-00…'` (and empty) → **`null`**, so a zero date does not
  become a bogus `DateTime`.
- **`Type::TimeInterval` (a `TIME` column) drops fractional seconds.** It parses with
  `#^(-?)(\d+)\D(\d+)\D(\d+)\z#` into `H`/`M`/`S` only, so a `TIME(3)` value's microseconds
  fall outside the pattern and are lost.
- **JSON** decodes by the configured format (`'string'`/legacy `'native'` → raw text,
  otherwise `json_decode` to array/object). The fallback for an unknown type **throws**.

Note the nullable-string coercion (`%sN`) is a **translator-side** concern (see
translator.md), not a normalize-side one — there is no `normalize` counterpart. Formats
per type come from `Connection::setFormats()`.
