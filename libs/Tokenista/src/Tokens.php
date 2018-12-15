<?php declare(strict_types = 1);

namespace Stylist\Tokenista;

use Stylist\Arrays\Arrays;


final class Tokens implements \IteratorAggregate, \ArrayAccess, \Countable
{

	/** @var Token[] */
	private $tokens;


	/**
	 * @internal
	 * @param Token[] $tokens
	 */
	public function __construct(array $tokens)
	{
		$this->tokens = $tokens;
	}


	public static function from(string $code): self
	{
		try {
			$tokens = \token_get_all($code, \TOKEN_PARSE);

		} catch (\ParseError $e) {
			// with TOKEN_PARSE flag, the function throws on invalid code
			// let's just ignore the error and tokenize the code without the flag
			$tokens = \token_get_all($code);
		}

		foreach ($tokens as $index => $token) {
			if ( ! \is_array($token)) {
				$previousIndex = $index - 1;
				$previousToken = $tokens[$previousIndex];

				/** @var Token $previousToken */

				// compute line number, in hope that CR-only line endings don't exist anymore
				$line = $previousToken->getLine() + \substr_count($previousToken->getValue(), "\n");
				$token = [
					Token::T_UNKNOWN,
					$token,
					$line
				];
			}

			$tokens[$index] = new Token($token[0], $token[1], $token[2], $index);
		}

		return new self($tokens);
	}


	public function assemble(): string
	{
		$string = '';
		foreach ($this->tokens as $token) {
			$string .= $token->getValue();
		}

		return $string;
	}


	public function __toString(): string
	{
		return $this->assemble();
	}


	public function find(Query $query): Tokens
	{
		$found = [];
		foreach ($this->tokens as $token) {
			if ($query->matches($token)) {
				$found[] = $token;
			}
		}

		return new self($found);
	}


	public function findOne(Query $query): ?Token
	{
		foreach ($this->tokens as $token) {
			if ($query->matches($token)) {
				return $token;
			}
		}

		return null;
	}


	public function subset(Token $start, Token $end): Tokens
	{
		if ($start->getIndex() >= $end->getIndex()) {
			throw new \InvalidArgumentException('$start index must be before $end index.');
		}

		$query = (new Query())
			->indexGreaterThanOrEqual($start->getIndex())
			->indexLesserThanOrEqual($end->getIndex());

		return $this->find($query);
	}


	public function getFirst(): Token
	{
		return Arrays::head($this->tokens);
	}


	public function getLast(): Token
	{
		return Arrays::last($this->tokens);
	}


	public function expect(int $initialIndex = 0): Expectation
	{
		return new Expectation($this, \max($initialIndex, 0));
	}


	// immutable changes

	public function remove(Token $token): self
	{
		$tokens = Arrays::remove($this->tokens, $token);
		return new self($tokens);
	}


	public function replace(Token $token, string $value): self
	{
		$tokens = Arrays::replace($this->tokens, $token, Token::literal($value));
		return new self($tokens);
	}


	public function prependTo(Token $token, string $value): self
	{
		$newValue = $value . $token->getValue();
		return $this->replace($token, $newValue);
	}


	public function appendTo(Token $token, string $value): self
	{
		$newValue = $token->getValue() . $value;
		return $this->replace($token, $newValue);
	}


	// \IteratorAggregate

	public function getIterator(): \Traversable
	{
		return new \ArrayIterator($this->tokens);
	}


	// \ArrayAccess

	public function offsetExists($offset): bool
	{
		return isset($this->tokens[$offset]);
	}


	public function offsetGet($offset): ?Token
	{
		return $this->tokens[$offset] ?? null;
	}


	public function offsetSet($offset, $value): void
	{
		throw new UnsupportedOperationException(
			'Modifying Tokens via ArrayAccess is not supported, use replace() method instead.'
		);
	}


	public function offsetUnset($offset): void
	{
		throw new UnsupportedOperationException(
			'Modifying Tokens via ArrayAccess is not supported, use remove() method instead.'
		);
	}


	// \Countable

	public function count(): int
	{
		return \count($this->tokens);
	}

}
