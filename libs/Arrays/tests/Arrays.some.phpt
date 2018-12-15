<?php declare(strict_types = 1);

namespace Stylist\Arrays\Tests;

use Stylist\Arrays\Arrays;
use Tester\Assert;


require_once __DIR__ . '/bootstrap.php';


(static function (): void {
	$array = [41, 42, 43, 44, 45];
	$some = Arrays::some($array, static function (int $number): bool {
		return $number > 44;
	});

	Assert::true($some);
})();


(static function (): void {
	$array = [41, 42, 43, 44, 45];
	$some = Arrays::some($array, static function (int $number): bool {
		return $number > 45;
	});

	Assert::false($some);
})();


(static function (): void {
	$array = [];
	$some = Arrays::some($array, static function (int $number): bool {
		return $number > 45;
	});

	Assert::false($some);
})();
