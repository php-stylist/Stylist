<?php declare(strict_types = 1);

namespace Stylist\Arrays\Tests;

use Stylist\Arrays\Arrays;
use Tester\Assert;


require_once __DIR__ . '/bootstrap.php';


(static function (): void {
	$array = [41, 42, 43, 44, 45];

	$arrayWithPrependedValue = Arrays::prepend($array, 40);
	Assert::same([40, 41, 42, 43, 44, 45], $arrayWithPrependedValue);
	Assert::same([41, 42, 43, 44, 45], $array);
})();
