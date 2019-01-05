<?php declare(strict_types = 1);

namespace Stylist\Tokenista;

use Stylist\Arrays\Arrays;


final class Query
{

	/** @var callable[] */
	private $matchers = [];


	public function typeIs(int ...$types): self
	{
		$this->matchers[] = static function (Token $token) use ($types): bool {
			return \in_array($token->getType(), $types, true);
		};

		return $this;
	}


	public function typeNot(int ...$types): self
	{
		$this->matchers[] = static function (Token $token) use ($types): bool {
			return ! \in_array($token->getType(), $types, true);
		};

		return $this;
	}


	public function valueIs(string ...$values): self
	{
		$this->matchers[] = static function (Token $token) use ($values): bool {
			return \in_array($token->getValue(), $values, true);
		};

		return $this;
	}


	public function valueNot(string ...$values): self
	{
		$this->matchers[] = static function (Token $token) use ($values): bool {
			return ! \in_array($token->getValue(), $values, true);
		};

		return $this;
	}


	public function valueLike(string $pattern): self
	{
		$this->matchers[] = static function (Token $token) use ($pattern): bool {
			return (bool) \preg_match($pattern, $token->getValue());
		};

		return $this;
	}


	public function valueNotLike(string $pattern): self
	{
		$this->matchers[] = static function (Token $token) use ($pattern): bool {
			return ! \preg_match($pattern, $token->getValue());
		};

		return $this;
	}


	public function indexIs(int ...$indices): self
	{
		$this->matchers[] = static function (Token $token) use ($indices): bool {
			return \in_array($token->getIndex(), $indices, true);
		};

		return $this;
	}


	public function indexNot(int ...$indices): self
	{
		$this->matchers[] = static function (Token $token) use ($indices): bool {
			return ! \in_array($token->getIndex(), $indices, true);
		};

		return $this;
	}


	public function indexGreaterThan(int $index): self
	{
		$this->matchers[] = function (Token $token) use ($index): bool {
			return $token->getIndex() > $index;
		};

		return $this;
	}


	public function indexGreaterThanOrEqual(int $index): self
	{
		$this->matchers[] = function (Token $token) use ($index): bool {
			return $token->getIndex() >= $index;
		};

		return $this;
	}


	public function indexLesserThan(int $index): self
	{
		$this->matchers[] = function (Token $token) use ($index): bool {
			return $token->getIndex() < $index;
		};

		return $this;
	}


	public function indexLesserThanOrEqual(int $index): self
	{
		$this->matchers[] = function (Token $token) use ($index): bool {
			return $token->getIndex() <= $index;
		};

		return $this;
	}


	public function lineIs(int ...$lines): self
	{
		$this->matchers[] = static function (Token $token) use ($lines): bool {
			return \in_array($token->getLine(), $lines, true);
		};

		return $this;
	}


	public function lineNot(int ...$lines): self
	{
		$this->matchers[] = static function (Token $token) use ($lines): bool {
			return ! \in_array($token->getLine(), $lines, true);
		};

		return $this;
	}


	public function lineGreaterThan(int $line): self
	{
		$this->matchers[] = function (Token $token) use ($line): bool {
			return $token->getLine() > $line;
		};

		return $this;
	}


	public function lineGreaterThanOrEqual(int $line): self
	{
		$this->matchers[] = function (Token $token) use ($line): bool {
			return $token->getLine() >= $line;
		};

		return $this;
	}


	public function lineLesserThan(int $line): self
	{
		$this->matchers[] = function (Token $token) use ($line): bool {
			return $token->getLine() < $line;
		};

		return $this;
	}


	public function lineLesserThanOrEqual(int $line): self
	{
		$this->matchers[] = function (Token $token) use ($line): bool {
			return $token->getLine() <= $line;
		};

		return $this;
	}


	public function custom(callable $matcher): self
	{
		$this->matchers[] = $matcher;
		return $this;
	}


	public function matches(Token $token): bool
	{
		return Arrays::every($this->matchers, static function (callable $matcher) use ($token): bool {
			return $matcher($token);
		});
	}

}
