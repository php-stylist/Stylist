<?php declare(strict_types = 1);

namespace Stylist\Tokenista;


final class Token
{

	public const T_UNKNOWN = -1;

	/** @var int */
	private $type;

	/** @var string */
	private $value;

	/** @var int */
	private $line;

	/** @var int */
	private $index;


	public function __construct(
		int $type,
		string $value,
		int $line,
		int $index
	)
	{
		$this->type = $type;
		$this->value = $value;
		$this->line = $line;
		$this->index = $index;
	}


	public static function literal(string $value): self
	{
		return new self(
			self::T_UNKNOWN,
			$value,
			-1,
			-1
		);
	}


	public function getType(): int
	{
		return $this->type;
	}


	public function getName(): string
	{
		return \token_name($this->type);
	}


	public function getValue(): string
	{
		return $this->value;
	}


	public function getLine(): int
	{
		return $this->line;
	}


	public function getIndex(): int
	{
		return $this->index;
	}

}
