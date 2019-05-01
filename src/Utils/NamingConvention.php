<?php declare(strict_types = 1);

namespace Stylist\Utils;

use Nette\Utils\Strings;


final class NamingConvention
{

	/** @var string */
	private $description;

	/** @var string */
	private $pattern;


	public function __construct(string $description, string $pattern)
	{
		$this->description = $description;
		$this->pattern = $pattern;
	}


	public static function pascalCased(): self
	{
		return new self('PascalCased', '/^([A-Z][a-z]*|[0-9]+)+$/');
	}


	public static function camelCased(): self
	{
		return new self('camelCased', '/^[a-z]+([A-Z][a-z]+|[A-Z]+|[0-9]+)*$/');
	}


	public static function snakeLowerCased(): self
	{
		return new self('snake_lower_cased', '/^([a-z0-9]+_?)+$/');
	}


	public static function snakeUpperCased(): self
	{
		return new self('SNAKE_UPPER_CASED', '/^([A-Z0-9]+_?)+$/');
	}


	public function matches(string $name): bool
	{
		return (bool) Strings::match($name, $this->pattern);
	}


	public function describe(): string
	{
		return $this->description;
	}

}
