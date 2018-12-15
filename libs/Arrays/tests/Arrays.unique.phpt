<?php declare(strict_types = 1);

namespace Stylist\Arrays\Tests;

use Stylist\Arrays\Arrays;
use Stylist\Arrays\EmptyArrayException;
use Tester\Assert;


require_once __DIR__ . '/bootstrap.php';


(static function (): void {
	$array = [41, 42, 43, 42, 44, 45, 42];
	$unique = Arrays::unique($array);
	Assert::same([41, 42, 43, 44, 45], $unique);
	Assert::same([41, 42, 43, 42, 44, 45, 42], $array);
})();


(static function (): void {
	$unique = Arrays::unique([]);
	Assert::same([], $unique);
})();
