<?php declare(strict_types=1);

use Tester\Assert;

require __DIR__ . '/bootstrap.php';


function createConnectionWithThrowingListener(array &$events, bool $lazy = false): Dibi\Connection
{
	$conn = new Dibi\Connection(['driver' => Dibi\Drivers\Dummy\Connection::class, 'lazy' => $lazy]);
	$conn->onEvent[] = function (Dibi\Event $event) use (&$events): void {
		$events[] = [$event->type, $event->result];
		throw new Dibi\DriverException('listener failed');
	};

	return $conn;
}


test('connect() is not reported as failed when a listener throws', function () {
	$events = [];
	$conn = createConnectionWithThrowingListener($events, lazy: true);

	Assert::exception(fn() => $conn->connect(), Dibi\DriverException::class, 'listener failed');
	Assert::same([[Dibi\Event::CONNECT, null]], $events);
});


test('begin() is not reported as failed when a listener throws', function () {
	$events = [];
	$conn = createConnectionWithThrowingListener($events);

	Assert::exception(fn() => $conn->begin(), Dibi\DriverException::class, 'listener failed');
	Assert::same([[Dibi\Event::BEGIN, null]], $events);
});


test('commit() is not reported as failed when a listener throws', function () {
	$events = [];
	$conn = createConnectionWithThrowingListener($events);

	Assert::exception(fn() => $conn->commit(), Dibi\DriverException::class, 'listener failed');
	Assert::same([[Dibi\Event::COMMIT, null]], $events);
});


test('rollback() is not reported as failed when a listener throws', function () {
	$events = [];
	$conn = createConnectionWithThrowingListener($events);

	Assert::exception(fn() => $conn->rollback(), Dibi\DriverException::class, 'listener failed');
	Assert::same([[Dibi\Event::ROLLBACK, null]], $events);
});
