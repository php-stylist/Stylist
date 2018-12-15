<?php declare(strict_types = 1);

namespace Stylist\Arrays;


final class Arrays
{

	private function __construct()
	{
	}


	/**
	 * Appends $item to $array and returns the resulting array.
	 */
	public static function append(array $array, $item): array
	{
		// make sure we don't mangle with the array
		$copy = $array;

		\array_push($copy, $item);
		return $copy;
	}


	/**
	 * Splits $array into chunks, each being an array with $size elements.
	 * The last chunk might contain less elements.
	 */
	public static function chunk(array $array, int $size): array
	{
		return \array_chunk($array, $size);
	}


	/**
	 * Merges $arrays together and returns the resulting array.
	 */
	public static function concat(array ...$arrays): array
	{
		return \array_merge(...$arrays);
	}


	/**
	 * Returns whether $value is contained within $array at least once.
	 */
	public static function contains(array $array, $item): bool
	{
		return \in_array($item, $array, true);
	}


	/**
	 * Calls given $callback for each element of $array. It does not modify the array.
	 */
	public static function each(array $array, callable $callback): void
	{
		foreach ($array as $item) {
			$callback($item);
		}
	}


	/**
	 * Returns whether $predicate is true for all elements of $array.
	 */
	public static function every(array $array, callable $predicate): bool
	{
		foreach ($array as $item) {
			if ($predicate($item) === false) {
				return false;
			}
		}

		return true;
	}


	/**
	 * Returns a new array containing only the elements of $array for which $predicate is true.
	 */
	public static function filter(array $array, callable $predicate): array
	{
		return \array_values(\array_filter($array, $predicate));
	}


	/**
	 * Returns the first element of $array for which $predicate is true,
	 * or FALSE if it's not true for any element of $array.
	 */
	public static function find(array $array, callable $predicate)
	{
		try {
			return self::head(
				self::filter($array, $predicate)
			);

		} catch (EmptyArrayException $e) {
			return false;
		}
	}


	/**
	 * Returns the first element of $array. Throws {@see EmptyArrayException} if invoked with an empty array.
	 *
	 * @throws EmptyArrayException
	 */
	public static function head(array $array)
	{
		// make sure we don't mangle with the array
		$copy = $array;

		$head = \reset($copy);
		if ($head === false) {
			throw new EmptyArrayException(__FUNCTION__);
		}

		return $head;
	}


	/**
	 * Returns all elements of $array except the last one.
	 */
	public static function init(array $array): array
	{
		return \array_slice($array, 0, -1);
	}


	/**
	 * Returns the last element of $array. Throws {@see EmptyArrayException} if invoked with an empty array.
	 *
	 * @throws EmptyArrayException
	 */
	public static function last(array $array)
	{
		// make sure we don't mangle with the array
		$copy = $array;

		$last = \end($copy);
		if ($last === false) {
			throw new EmptyArrayException(__FUNCTION__);
		}

		return $last;
	}


	/**
	 * Returns an array containing all elements of $array after applying $callback to each one.
	 */
	public static function map(array $array, callable $callback): array
	{
		return \array_map($callback, $array);
	}


	/**
	 * Prepends $item to $array and returns the resulting array.
	 */
	public static function prepend(array $array, $item): array
	{
		// make sure we don't mangle with the array
		$copy = $array;

		\array_unshift($copy, $item);
		return $copy;
	}


	/**
	 * Applies $reducer to the elements of $array iteratively to reduce the array to a single value.
	 */
	public static function reduce(array $array, callable $reducer, $initialValue)
	{
		return \array_reduce($array, $reducer, $initialValue);
	}


	/**
	 * Applies $reducer to the elements of $array, in reversed order, iteratively to reduce the array to a single value.
	 */
	public static function reduceRight(array $array, callable $reducer, $initialValue)
	{
		return self::reduce(self::reverse($array), $reducer, $initialValue);
	}


	/**
	 * Removes the first occurrence of $value from $array and returns the resulting array.
	 * It does nothing if $value is not found in $array.
	 */
	public static function remove(array $array, $value): array
	{
		// make sure we don't mangle with the array
		$copy = $array;

		$index = \array_search($value, $copy, true);
		if ($index !== false) {
			unset($copy[$index]);
		}

		return \array_values($copy);
	}


	/**
	 * Removes all occurrences of $value from $array and returns the resulting array.
	 * It does nothing if $value is not found in $array.
	 */
	public static function removeAll(array $array, $value): array
	{
		// make sure we don't mangle with the array
		$copy = $array;

		while (self::contains($copy, $value)) {
			$index = \array_search($value, $copy, true);
			\assert($index !== false);
			unset($copy[$index]);
		}

		$array = \array_values($copy);
		return $array;
	}


	/**
	 * Replaces the first occurrence of $value in $array with $newValue and returns the resulting array.
	 * It does nothing if $value is not found in $array.
	 */
	public static function replace(array $array, $value, $newValue): array
	{
		// make sure we don't mangle with the array
		$copy = $array;

		$index = \array_search($value, $copy, true);
		if ($index !== false) {
			$copy[$index] = $newValue;
		}

		return \array_values($copy);
	}


	/**
	 * Replaces all occurrences of $value in $array with $newValue and returns the resulting array.
	 * It does nothing if $value is not found in $array.
	 */
	public static function replaceAll(array $array, $value, $newValue): array
	{
		// make sure we don't mangle with the array
		$copy = $array;

		while (self::contains($copy, $value)) {
			$index = \array_search($value, $copy, true);
			\assert($index !== false);
			$copy[$index] = $newValue;
		}

		$array = \array_values($copy);
		return $array;
	}


	/**
	 * Returns the $array with elements in reversed order.
	 */
	public static function reverse(array $array): array
	{
		return \array_reverse($array);
	}


	/**
	 * Extracts a slice of $array, starting from $offset and with a length of $length,
	 * or to the end of the array if length is not provided.
	 */
	public static function slice(array $array, int $offset, ?int $length = null): array
	{
		return \array_slice($array, $offset, $length);
	}


	/**
	 * Returns whether $predicate is true for at least one element of $array.
	 */
	public static function some(array $array, callable $predicate): bool
	{
		foreach ($array as $item) {
			if ($predicate($item) === true) {
				return true;
			}
		}

		return false;
	}


	/**
	 * Sorts $array using provided $comparator and returns the sorted array.
	 * If the comparator is omitted, it defaults to {@see Sorter::ascending()} comparator.
	 */
	public static function sort(array $array, ?callable $comparator = null): array
	{
		$comparator = $comparator ?? Sorter::ascending();

		// make sure we don't mangle with the array
		$copy = $array;

		\usort($copy, $comparator);
		return \array_values($copy);
	}


	/**
	 * Returns all elements of $array except the first one.
	 */
	public static function tail(array $array): array
	{
		return \array_slice($array, 1);
	}


	/**
	 * Returns all unique elements of $array.
	 */
	public static function unique(array $array): array
	{
		return \array_values(\array_unique($array));
	}

}
