<?php declare(strict_types = 1);

namespace Stylist\Arrays\Tests;

use Stylist\Arrays\Arrays;
use Tester\Assert;


require_once __DIR__ . '/bootstrap.php';


(static function (): void {
	$array = [41, 42, 43, 44, 45];
	$filtered = Arrays::filter($array, static function (int $number): bool {
		return $number > 43;
	});

	Assert::same([44, 45], $filtered);
	Assert::same([41, 42, 43, 44, 45], $array);
})();
