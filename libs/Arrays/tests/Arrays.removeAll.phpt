<?php declare(strict_types = 1);

namespace Stylist\Arrays\Tests;

use Stylist\Arrays\Arrays;
use Tester\Assert;


require_once __DIR__ . '/bootstrap.php';


(static function (): void {
	$array = [41, 42, 43, 44, 45, 45];

	$arrayWithRemovedValue = Arrays::removeAll($array, 45);
	Assert::same([41, 42, 43, 44], $arrayWithRemovedValue);
	Assert::same([41, 42, 43, 44, 45, 45], $array);
})();


(static function (): void {
	$array = [41, 42, 43, 44, 45];

	$arrayWithRemovedValue = Arrays::removeAll($array, 46);
	Assert::same([41, 42, 43, 44, 45], $arrayWithRemovedValue);
	Assert::same([41, 42, 43, 44, 45], $array);
})();
