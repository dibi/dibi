# Translator (the SQL builder)

`Translator` is the most complex component: it assembles the final SQL from a mix of
literal fragments, `%modifier` placeholders, and values whose type is auto-detected.

## `formatValue` and modifiers

`formatValue(mixed $value, ?string $modifier): string` is the central dispatch. With a
modifier, a large `switch` handles the scalar ones (`s`, `sN`/`sn`, `i`, `iN`, `f`, `d`,
`t`, `dt`, `n`, `b`, `bin`, `like`, `ex`, `sql`/`SQL`, …) and a separate `switch` handles
the array modifiers (`and`, `or`, `a`, `v`, `l`, `in`, `by`, `m`, `ex`). Without a
modifier the type is detected at the end (`is_string`→`escapeText`, `is_int`, `is_float`→
`number_format`, `is_bool`→`escapeBool`, `null`→`'NULL'`, `DateTimeInterface`,
`DateInterval`, `Literal`, `Expression`). For an associative array with no modifier,
INSERT-vs-SET is chosen by the **command prefix** — the first six chars of the SQL
(`INSERT`/`REPLAC`) select VALUES/LIST, otherwise SET.

**Trap — `%sN`/`%iN` turn `0` into `NULL`.** The nullable modifiers test
`$value === '' || $value === 0 || $value === null` → `'NULL'`. Because it is strict
`=== 0`, an **integer `0` and an empty string become `NULL`**, while the *string* `'0'`
stays text and `0.0` does not (PHP `0.0 === 0` is false) — an easy, inconsistent footgun.

## Errors travel in-band as `**text**`

Because `formatValue` returns `: string` and is called **inline** while the SQL string is
being assembled, it must return *something* even on error. So an error is emitted as a
magic marker via an assignment-expression idiom:

```php
return $this->errors[] = "**Unknown or unexpected modifier %$modifier**";
```

— which simultaneously (a) inserts the `**…**` marker into the SQL being built and
(b) records it in `$this->errors`. **The decision to throw is driven by the `$errors`
array, not by scanning the SQL for `**`** (a common misreading): after building,
`translate()` does `if ($this->errors) { throw new Exception('SQL translate error: ' .
trim(reset($this->errors), '*'), 0, $sql); }`. The `**` are just delimiters stripped by
`trim(..., '*')`, and the exception's `getSql()` carries the SQL *with* the markers still
embedded — so a collision with legitimate `**` content is cosmetic (in the reported SQL),
not a correctness issue. Note `formatValue` short-circuits to `'...'` when
`$this->comment` is set (an "off" `%if` branch), so no errors are generated there.

An agent editing this must keep the invariant: **every `formatValue` path returns a
string and records real errors in `$errors`** — returning early or throwing mid-assembly
breaks the SQL string being built.
