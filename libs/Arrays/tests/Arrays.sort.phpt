<?php declare(strict_types = 1);

namespace Stylist\Arrays\Tests;

use Stylist\Arrays\Arrays;
use Tester\Assert;


require_once __DIR__ . '/bootstrap.php';


(static function (): void {
	$array = [43, 41, 45, 44, 42];

	$sorted = Arrays::sort($array);
	Assert::same([41, 42, 43, 44, 45], $sorted);
	Assert::same([43, 41, 45, 44, 42], $array);
})();


(static function (): void {
	$array = [43, 41, 45, 44, 42];

	$comparator = static function (int $a, int $b): int {
		return $b <=> $a;
	};

	$sorted = Arrays::sort($array, $comparator);
	Assert::same([45, 44, 43, 42, 41], $sorted);
	Assert::same([43, 41, 45, 44, 42], $array);
})();
