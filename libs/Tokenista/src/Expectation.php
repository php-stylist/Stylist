<?php declare(strict_types = 1);

namespace Stylist\Tokenista;


final class Expectation
{

	/** @var Tokens */
	private $tokens;

	/** @var int */
	private $index;

	/** @var bool */
	private $met = true;


	public function __construct(
		Tokens $tokens,
		int $index
	)
	{
		$this->tokens = $tokens;
		$this->index = $index;
	}


	public function expect(Query $query): ?Token
	{
		$token = $this->tokens[$this->index];
		if ($token !== null && $query->matches($token)) {
			$this->index++;
			return $token;
		}

		$this->met = false;
		return null;
	}


	public function maybe(Query $query): ?Token
	{
		$token = $this->tokens[$this->index];
		if ($token !== null && $query->matches($token)) {
			$this->index++;
			return $token;
		}

		return null;
	}


	public function search(Query $query): ?Token
	{
		$index = $this->index;

		$token = $this->tokens[$index];
		while ($token !== null) {
			if ($query->matches($token)) {
				$this->index = $index + 1;
				return $token;
			}

			$token = $this->tokens[++$index];
		}

		$this->met = false;
		return null;
	}


	public function section(Query $start, Query $end): ?Tokens
	{
		$first = $this->expect($start);
		if ($first === null) {
			return null;
		}

		$depth = 0;
		$this->index = $index = $first->getIndex();
		$token = $this->tokens[$index];
		while ($token !== null) {
			if ($start->matches($token)) {
				$depth++;

			} elseif ($end->matches($token)) {
				$depth--;

				if ($depth === 0) {
					$this->index = $token->getIndex() + 1;
					return $this->tokens->subset($first, $token);
				}
			}

			$token = $this->tokens[++$index];
		}

		$this->met = false;
		return null;
	}


	public function while(Query $query): ?Tokens
	{
		$index = $this->index;
		$tokens = [];

		$token = $this->tokens[$index];
		while ($token !== null && $query->matches($token)) {
			$tokens[] = $token;
			$token = $this->tokens[++$index];
		}

		if (\count($tokens) === 0) {
			$this->met = false;
			return null;
		}

		$this->index = $index;
		return new Tokens($tokens);
	}


	public function until(Query $query): ?Tokens
	{
		$index = $this->index;
		$tokens = [];

		$token = $this->tokens[$index];
		while ($token !== null && ! $query->matches($token)) {
			$tokens[] = $token;
			$token = $this->tokens[++$index];
		}

		if (\count($tokens) === 0) {
			$this->met = false;
			return null;
		}

		$this->index = $index;
		return new Tokens($tokens);
	}


	public function getIndex(): int
	{
		return $this->index;
	}


	public function met(): bool
	{
		return $this->met;
	}

}
