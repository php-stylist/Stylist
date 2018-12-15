<?php declare(strict_types = 1);

namespace Stylist\Arrays\Tests;

use Stylist\Arrays\Arrays;
use Tester\Assert;


require_once __DIR__ . '/bootstrap.php';


(static function (): void {
	$array = [41, 42, 43, 44, 45];

	$reversed = Arrays::reverse($array);
	Assert::same([45, 44, 43, 42, 41], $reversed);
	Assert::same([41, 42, 43, 44, 45], $array);
})();


(static function (): void {
	$array = [];
	Assert::same([], Arrays::reverse($array));
})();
