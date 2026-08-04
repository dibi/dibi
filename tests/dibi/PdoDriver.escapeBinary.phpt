<?php declare(strict_types=1);

use Tester\Assert;

require __DIR__ . '/bootstrap.php';


// SQLite: escapeBinary() must return a hex literal (X'...'), matching the native
// sqlite3 driver. Without a dedicated 'sqlite' branch it falls through to
// PDO::quote(), which returns false for values containing NUL bytes on PHP 8.5+,
// surfacing as a TypeError because escapeBinary() is typed : string.

$pdo = new PDO('sqlite::memory:');
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_SILENT);
$driver = new Dibi\Drivers\PdoDriver(['resource' => $pdo]);

Assert::same("X'0001ff'", $driver->escapeBinary("\x00\x01\xff"));
Assert::same("X''", $driver->escapeBinary(''));
