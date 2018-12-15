<?php declare(strict_types = 1);

namespace Stylist\Tokenista\Tests;

use Stylist\Tokenista\Token;
use Tester\Assert;
use Tester\TestCase;


require_once __DIR__ . '/bootstrap.php';


/**
 * @testCase
 */
final class TokenTest extends TestCase
{

	public function testToken(): void
	{
		$token = new Token(\T_CLASS, 'class', 5, 11);

		Assert::same(\T_CLASS, $token->getType());
		Assert::same('T_CLASS', $token->getName());
		Assert::same('class', $token->getValue());
		Assert::same(5, $token->getLine());
		Assert::same(11, $token->getIndex());
	}


	public function testLiteral(): void
	{
		$literal = Token::literal('[]');

		Assert::same(Token::T_UNKNOWN, $literal->getType());
		Assert::same('UNKNOWN', $literal->getName());
		Assert::same('[]', $literal->getValue());
		Assert::same(-1, $literal->getLine());
		Assert::same(-1, $literal->getIndex());
	}

}


(new TokenTest())->run();
