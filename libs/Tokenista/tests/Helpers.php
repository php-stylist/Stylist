<?php declare(strict_types = 1);

namespace Stylist\Tokenista\Tests;

use Stylist\Tokenista\Token;
use Tester\Assert;


final class Helpers
{

	private function __construct()
	{
	}


	public static function assertToken(Token $expectedToken, Token $actualToken): void
	{
		Assert::true(
			$expectedToken->getType() === $actualToken->getType()
			&& $expectedToken->getValue() === $actualToken->getValue()
			&& $expectedToken->getLine() === $actualToken->getLine()
			&& $expectedToken->getIndex() === $actualToken->getIndex(),
			\sprintf(
				'Token [%s, %s, %d, %d] does not match expected token [%s, %s, %d, %d]',
				$actualToken->getName(),
				$actualToken->getValue(),
				$actualToken->getLine(),
				$actualToken->getIndex(),
				$expectedToken->getName(),
				$expectedToken->getValue(),
				$expectedToken->getLine(),
				$expectedToken->getIndex()
			)
		);
	}

}
