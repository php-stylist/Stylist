<?php declare(strict_types = 1);

namespace Stylist\Arrays\Tests;

use Stylist\Arrays\Sorter;
use Tester\Assert;


require_once __DIR__ . '/bootstrap.php';


(static function (): void {
	$comparator = Sorter::descending();

	Assert::same(1, $comparator(41, 42));
	Assert::same(0, $comparator(42, 42));
	Assert::same(-1, $comparator(42, 41));
})();
