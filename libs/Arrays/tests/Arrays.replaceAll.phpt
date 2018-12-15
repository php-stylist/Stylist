<?php declare(strict_types = 1);

namespace Stylist\Arrays\Tests;

use Stylist\Arrays\Arrays;
use Tester\Assert;


require_once __DIR__ . '/bootstrap.php';


(static function (): void {
	$array = [41, 42, 43, 44, 45, 45];

	$arrayWithReplacedValue = Arrays::replaceAll($array, 45, '45');
	Assert::same([41, 42, 43, 44, '45', '45'], $arrayWithReplacedValue);
	Assert::same([41, 42, 43, 44, 45, 45], $array);
})();


(static function (): void {
	$array = [41, 42, 43, 44, 45];

	$arrayWithReplacedValue = Arrays::replace($array, 46, '46');
	Assert::same([41, 42, 43, 44, 45], $arrayWithReplacedValue);
	Assert::same([41, 42, 43, 44, 45], $array);
})();
