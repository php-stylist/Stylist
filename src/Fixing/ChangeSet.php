<?php declare(strict_types = 1);

namespace Stylist\Fixing;

use Stylist\Tokenista\Token;
use Stylist\Tokenista\Tokens;


final class ChangeSet
{

	/** @var Change[] */
	private $changes = [];


	public function removeToken(Token $token): void
	{
		$this->changes[] = Change::remove($token);
	}


	public function removeTokens(Tokens $tokens): void
	{
		foreach ($tokens as $token) {
			$this->removeToken($token);
		}
	}


	public function replaceToken(Token $token, string $value): void
	{
		$this->changes[] = Change::replace($token, $value);
	}


	public function replaceTokens(Tokens $tokens, string $value): void
	{
		foreach ($tokens as $token) {
			if ($tokens->getFirst() === $token) {
				$this->replaceToken($token, $value);

			} else {
				$this->removeToken($token);
			}
		}
	}


	public function prependTo(Token $token, string $value): void
	{
		$this->changes[] = Change::prependTo($token, $value);
	}


	public function appendTo(Token $token, string $value): void
	{
		$this->changes[] = Change::appendTo($token, $value);
	}


	/**
	 * @throws ConflictingChangesException
	 */
	public function merge(ChangeSet $changeSet): void
	{
		$currentChanges = $this->changes;

		foreach ($changeSet->changes as $change) {
			foreach ($currentChanges as $currentChange) {
				if ($change->conflictsWith($currentChange)) {
					$this->changes = $currentChanges;
					throw ConflictingChangesException::fromChanges($currentChange, $change);
				}
			}

			$this->changes[] = $change;
		}
	}


	public function apply(Tokens $tokens): Tokens
	{
		foreach ($this->changes as $change) {
			$tokens = $change->apply($tokens);
		}

		return $tokens;
	}

}
