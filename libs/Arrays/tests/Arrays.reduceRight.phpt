<?php declare(strict_types = 1);

namespace Stylist\Arrays\Tests;

use Stylist\Arrays\Arrays;
use Tester\Assert;


require_once __DIR__ . '/bootstrap.php';


(static function (): void {
	$array = ['a', 'b', 'c'];
	$reduced = Arrays::reduceRight($array, static function (string $carry, string $item): string {
		return $carry . $item;
	}, '');

	Assert::same('cba', $reduced);
	Assert::same(['a', 'b', 'c'], $array);
})();
