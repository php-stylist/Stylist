<?php declare(strict_types = 1);

namespace Stylist\Arrays\Tests;

use Stylist\Arrays\Arrays;
use Tester\Assert;


require_once __DIR__ . '/bootstrap.php';


(static function (): void {
	$array = [41, 42, 43, 44, 45];

	$slice = Arrays::slice($array, 1, 3);
	Assert::same([42, 43, 44], $slice);
	Assert::same([41, 42, 43, 44, 45], $array);
})();


(static function (): void {
	$array = [41, 42, 43, 44, 45];

	$slice = Arrays::slice($array, 1, -1);
	Assert::same([42, 43, 44], $slice);
	Assert::same([41, 42, 43, 44, 45], $array);
})();


(static function (): void {
	$array = [41, 42, 43, 44, 45];

	$slice = Arrays::slice($array, -1);
	Assert::same([45], $slice);
	Assert::same([41, 42, 43, 44, 45], $array);
})();


(static function (): void {
	$array = [];
	Assert::same([], Arrays::slice($array, 1, 3));
})();
