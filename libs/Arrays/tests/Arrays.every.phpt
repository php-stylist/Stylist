<?php declare(strict_types = 1);

namespace Stylist\Arrays\Tests;

use Stylist\Arrays\Arrays;
use Tester\Assert;


require_once __DIR__ . '/bootstrap.php';


(static function (): void {
	$array = [41, 42, 43, 44, 45];
	$every = Arrays::every($array, static function (int $number): bool {
		return $number > 44;
	});

	Assert::false($every);
})();


(static function (): void {
	$array = [41, 42, 43, 44, 45];
	$every = Arrays::every($array, static function (int $number): bool {
		return $number > 40;
	});

	Assert::true($every);
})();


(static function (): void {
	$array = [];
	$every = Arrays::every($array, static function (int $number): bool {
		return $number > 45;
	});

	Assert::true($every);
})();
