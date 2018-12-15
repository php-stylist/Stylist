<?php declare(strict_types = 1);

namespace Stylist\Tokenista\Tests;

use Stylist\Tokenista\Expectation;
use Stylist\Tokenista\Query;
use Stylist\Tokenista\Token;
use Stylist\Tokenista\Tokens;
use Stylist\Tokenista\UnsupportedOperationException;
use Tester\Assert;
use Tester\TestCase;


require_once __DIR__ . '/bootstrap.php';


/**
 * @testCase
 */
final class TokensTest extends TestCase
{

	/**
	 * @dataProvider provideTokens
	 */
	public function testFrom(string $code, array $expectedTokens): void
	{
		$tokens = Tokens::from($code);

		foreach ($expectedTokens as $index => $expectedToken) {
			$actualToken = $tokens[$index];
			Helpers::assertToken($expectedToken, $actualToken);
		}
	}


	/**
	 * @dataProvider provideTokens
	 */
	public function testAssemble(string $expectedCode, array $rawTokens): void
	{
		$tokens = new Tokens($rawTokens);
		Assert::same($expectedCode, $tokens->assemble());
	}


	public function provideTokens(): iterable
	{
		yield [
			\file_get_contents(__DIR__ . '/fixtures/source_1.phps'),
			require __DIR__ . '/fixtures/source_1.php',
		];

		yield [
			\file_get_contents(__DIR__ . '/fixtures/source_2.phps'),
			require __DIR__ . '/fixtures/source_2.php',
		];

		yield [
			\file_get_contents(__DIR__ . '/fixtures/source_3.phps'),
			require __DIR__ . '/fixtures/source_3.php',
		];
	}


	public function testFind(): void
	{
		$rawTokens = require __DIR__ . '/fixtures/source_1.php';
		$tokens = new Tokens($rawTokens);

		$query = (new Query())->typeIs(\T_LNUMBER);
		$foundTokens = $tokens->find($query);

		Assert::count(3, $foundTokens);
		Helpers::assertToken(new Token(T_LNUMBER, '42', 3, 6), $foundTokens[0]);
		Helpers::assertToken(new Token(T_LNUMBER, '42', 4, 16), $foundTokens[1]);
		Helpers::assertToken(new Token(T_LNUMBER, '5', 5, 39), $foundTokens[2]);
	}


	public function testFindOne(): void
	{
		$rawTokens = require __DIR__ . '/fixtures/source_1.php';
		$tokens = new Tokens($rawTokens);

		$query = (new Query())->typeIs(\T_LNUMBER);
		$foundToken = $tokens->findOne($query);
		Helpers::assertToken(new Token(T_LNUMBER, '42', 3, 6), $foundToken);

		$query2 = (new Query())->typeIs(\T_INTERFACE);
		$notFoundToken = $tokens->findOne($query2);
		Assert::null($notFoundToken);
	}


	public function testSubset(): void
	{
		$rawTokens = require __DIR__ . '/fixtures/source_1.php';
		$tokens = new Tokens($rawTokens);

		$subset = $tokens->subset($tokens[2], $tokens[4]);
		Assert::count(3, $subset);
		Helpers::assertToken(new Token(T_VARIABLE, '$foo', 3, 2), $subset[0]);
		Helpers::assertToken(new Token(T_WHITESPACE, ' ', 3, 3), $subset[1]);
		Helpers::assertToken(new Token(Token::T_UNKNOWN, '=', 3, 4), $subset[2]);
	}


	public function testGetFirst(): void
	{
		$rawTokens = require __DIR__ . '/fixtures/source_1.php';
		$tokens = new Tokens($rawTokens);

		$first = $tokens->getFirst();
		Helpers::assertToken(new Token(\T_OPEN_TAG, "<?php\n", 1, 0), $first);
	}


	public function testGetLast(): void
	{
		$rawTokens = require __DIR__ . '/fixtures/source_1.php';
		$tokens = new Tokens($rawTokens);

		$last = $tokens->getLast();
		Helpers::assertToken(new Token(\T_WHITESPACE, "\n", 6, 44), $last);
	}


	public function testExpect(): void
	{
		$rawTokens = require __DIR__ . '/fixtures/source_1.php';
		$tokens = new Tokens($rawTokens);

		$expectation = $tokens->expect();
		Assert::type(Expectation::class, $expectation);
		Assert::same(0, $expectation->getIndex());

		$expectation = $tokens->expect(8);
		Assert::type(Expectation::class, $expectation);
		Assert::same(8, $expectation->getIndex());

		$expectation = $tokens->expect(-5);
		Assert::type(Expectation::class, $expectation);
		Assert::same(0, $expectation->getIndex());
	}


	public function testRemove(): void
	{
		$rawTokens = require __DIR__ . '/fixtures/source_1.php';
		$tokens = new Tokens($rawTokens);

		$newTokens = $tokens->remove($tokens[2]);
		Helpers::assertToken($tokens[2], new Token(\T_VARIABLE, '$foo', 3, 2));
		Helpers::assertToken($newTokens[2], $tokens[3]);
	}


	public function testReplace(): void
	{
		$rawTokens = require __DIR__ . '/fixtures/source_1.php';
		$tokens = new Tokens($rawTokens);

		$newTokens = $tokens->replace($tokens[2], '$bar');
		Helpers::assertToken($tokens[2], new Token(\T_VARIABLE, '$foo', 3, 2));
		Helpers::assertToken($newTokens[2], Token::literal('$bar'));
	}


	public function testPrependTo(): void
	{
		$rawTokens = require __DIR__ . '/fixtures/source_1.php';
		$tokens = new Tokens($rawTokens);

		$newTokens = $tokens->prependTo($tokens[2], 'Foo::');
		Helpers::assertToken($tokens[2], new Token(\T_VARIABLE, '$foo', 3, 2));
		Helpers::assertToken($newTokens[2], Token::literal('Foo::$foo'));
	}


	public function testAppendTo(): void
	{
		$rawTokens = require __DIR__ . '/fixtures/source_1.php';
		$tokens = new Tokens($rawTokens);

		$newTokens = $tokens->appendTo($tokens[2], 'Bar');
		Helpers::assertToken($tokens[2], new Token(\T_VARIABLE, '$foo', 3, 2));
		Helpers::assertToken($newTokens[2], Token::literal('$fooBar'));
	}


	public function testIteratorAggregate(): void
	{
		$rawTokens = require __DIR__ . '/fixtures/source_1.php';
		$tokens = new Tokens($rawTokens);

		$iterator = $tokens->getIterator();
		Assert::type(\ArrayIterator::class, $iterator);
		Assert::count(45, $iterator);
	}


	public function testArrayAccess(): void
	{
		$rawTokens = require __DIR__ . '/fixtures/source_1.php';
		$tokens = new Tokens($rawTokens);

		// offsetExists
		Assert::true(isset($tokens[2]));
		Assert::false(isset($tokens[59]));

		// offsetGet
		Helpers::assertToken(new Token(\T_VARIABLE, '$foo', 3, 2), $tokens[2]);
		Assert::null($tokens[59]);

		// offsetSet
		Assert::throws(static function () use ($tokens): void {
			$tokens[2] = Token::literal('42');
		}, UnsupportedOperationException::class);

		// offsetUnset
		Assert::throws(static function () use ($tokens): void {
			unset($tokens[2]);
		}, UnsupportedOperationException::class);
	}


	public function testCountable(): void
	{
		$rawTokens = require __DIR__ . '/fixtures/source_1.php';
		$tokens = new Tokens($rawTokens);

		Assert::count(45, $tokens);
	}

}


(new TokensTest())->run();
