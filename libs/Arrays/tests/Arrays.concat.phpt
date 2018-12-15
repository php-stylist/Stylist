<?php declare(strict_types = 1);

namespace Stylist\Arrays\Tests;

use Stylist\Arrays\Arrays;
use Tester\Assert;


require_once __DIR__ . '/bootstrap.php';


(static function (): void {
	$first = [41, 42, 43];
	$second = [44, 45];
	$third = [45, 46];

	$concatenated = Arrays::concat($first, $second, $third);
	Assert::same([41, 42, 43, 44, 45, 45, 46], $concatenated);
})();
