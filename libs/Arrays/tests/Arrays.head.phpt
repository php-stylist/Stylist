<?php declare(strict_types = 1);

namespace Stylist\Arrays\Tests;

use Stylist\Arrays\Arrays;
use Stylist\Arrays\EmptyArrayException;
use Tester\Assert;


require_once __DIR__ . '/bootstrap.php';


(static function (): void {
	$array = [41, 42, 43, 44, 45];
	$head = Arrays::head($array);
	Assert::same(41, $head);
	Assert::same([41, 42, 43, 44, 45], $array);
})();


(static function (): void {
	Assert::throws(static function (): void {
		Arrays::head([]);
	}, EmptyArrayException::class);
})();
