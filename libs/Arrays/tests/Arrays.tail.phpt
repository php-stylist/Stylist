<?php declare(strict_types = 1);

namespace Stylist\Arrays\Tests;

use Stylist\Arrays\Arrays;
use Stylist\Arrays\EmptyArrayException;
use Tester\Assert;


require_once __DIR__ . '/bootstrap.php';


(static function (): void {
	$array = [41, 42, 43, 44, 45];
	$tail = Arrays::tail($array);
	Assert::same([42, 43, 44, 45], $tail);
	Assert::same([41, 42, 43, 44, 45], $array);
})();


(static function (): void {
	$tail = Arrays::tail([]);
	Assert::same([], $tail);
})();
