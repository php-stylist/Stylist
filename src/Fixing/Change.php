<?php declare(strict_types = 1);

namespace Stylist\Fixing;

use Stylist\Tokenista\Token;
use Stylist\Tokenista\Tokens;


final class Change
{

	private const ACTION_REMOVE = 1;
	private const ACTION_REPLACE = 2;
	private const ACTION_PREPEND_TO = 3;
	private const ACTION_APPEND_TO = 4;

	/** @var Token */
	private $token;

	/** @var int */
	private $action;

	/** @var ?string */
	private $value;


	private function __construct(Token $token, int $action, ?string $value)
	{
		$this->token = $token;
		$this->action = $action;
		$this->value = $value;
	}


	public static function remove(Token $token): self
	{
		return new self($token, self::ACTION_REMOVE, null);
	}


	public static function replace(Token $token, string $value): self
	{
		return new self($token, self::ACTION_REPLACE, $value);
	}


	public static function prependTo(Token $token, string $value): self
	{
		return new self($token, self::ACTION_PREPEND_TO, $value);
	}


	public static function appendTo(Token $token, string $value): self
	{
		return new self($token, self::ACTION_APPEND_TO, $value);
	}


	public function conflictsWith(Change $change): bool
	{
		return $this->token === $change->token;
	}


	public function apply(Tokens $tokens): Tokens
	{
		if ($this->action === self::ACTION_REMOVE) {
			return $tokens->remove($this->token);
		}

		if ($this->action === self::ACTION_REPLACE) {
			\assert($this->value !== null);
			return $tokens->replace($this->token, $this->value);
		}

		if ($this->action === self::ACTION_PREPEND_TO) {
			\assert($this->value !== null);
			return $tokens->prependTo($this->token, $this->value);
		}

		if ($this->action === self::ACTION_APPEND_TO) {
			\assert($this->value !== null);
			return $tokens->appendTo($this->token, $this->value);
		}

		return $tokens;
	}

}
