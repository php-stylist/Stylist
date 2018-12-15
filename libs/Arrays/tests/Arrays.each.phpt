<?php declare(strict_types = 1);

namespace Stylist\Arrays\Tests;

use Stylist\Arrays\Arrays;
use Tester\Assert;


require_once __DIR__ . '/bootstrap.php';


(static function (): void {
	$array = [41, 42, 43, 44, 45];
	$new = [];

	Arrays::each($array, static function (int $number) use (&$new): void {
		$new[] = $number + 5;
	});

	Assert::same([46, 47, 48, 49, 50], $new);
	Assert::same([41, 42, 43, 44, 45], $array);
})();
