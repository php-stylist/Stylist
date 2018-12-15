<?php declare(strict_types = 1);

namespace Stylist\Arrays;


final class Sorter
{

	private function __construct()
	{
	}


	/**
	 * Creates a comparator function that sorts the values in ascending order.
	 */
	public static function ascending(): callable
	{
		return static function ($a, $b): int {
			return $a <=> $b;
		};
	}


	/**
	 * Creates a comparator function that sorts the values in descending order.
	 */
	public static function descending(): callable
	{
		return static function ($a, $b): int {
			return $b <=> $a;
		};
	}

}
