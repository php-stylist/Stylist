<?php declare(strict_types = 1);

namespace Stylist\Arrays\Tests;

use Stylist\Arrays\Arrays;
use Tester\Assert;


require_once __DIR__ . '/bootstrap.php';


(static function (): void {
	$array = [41, 42, 43, 44, 45];
	Assert::true(Arrays::contains($array, 44));
	Assert::false(Arrays::contains($array, 46));
	Assert::false(Arrays::contains($array, '44'));
})();
