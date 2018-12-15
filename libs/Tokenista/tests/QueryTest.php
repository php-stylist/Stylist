<?php declare(strict_types = 1);

namespace Stylist\Tokenista\Tests;

use Stylist\Tokenista\Query;
use Stylist\Tokenista\Token;
use Tester\Assert;
use Tester\TestCase;


require_once __DIR__ . '/bootstrap.php';


/**
 * @testCase
 */
final class QueryTest extends TestCase
{

	public function testTypeIs(): void
	{
		$query = (new Query())->typeIs(\T_CLASS);
		Assert::true($query->matches(new Token(\T_CLASS, 'class', 4, 4)));
		Assert::false($query->matches(new Token(\T_INTERFACE, 'interface', 4, 4)));

		$multiQuery = (new Query())->typeIs(\T_CLASS, \T_INTERFACE);
		Assert::true($multiQuery->matches(new Token(\T_CLASS, 'class', 4, 4)));
		Assert::true($multiQuery->matches(new Token(\T_INTERFACE, 'interface', 4, 4)));
		Assert::false($multiQuery->matches(new Token(\T_TRAIT, 'trait', 4, 4)));
	}


	public function testTypeNot(): void
	{
		$query = (new Query())->typeNot(\T_CLASS);
		Assert::false($query->matches(new Token(\T_CLASS, 'class', 4, 4)));
		Assert::true($query->matches(new Token(\T_INTERFACE, 'interface', 4, 4)));

		$multiQuery = (new Query())->typeNot(\T_CLASS, \T_INTERFACE);
		Assert::false($multiQuery->matches(new Token(\T_CLASS, 'class', 4, 4)));
		Assert::false($multiQuery->matches(new Token(\T_INTERFACE, 'interface', 4, 4)));
		Assert::true($multiQuery->matches(new Token(\T_TRAIT, 'trait', 4, 4)));
	}


	public function testValueIs(): void
	{
		$query = (new Query())->valueIs('class');
		Assert::true($query->matches(new Token(\T_CLASS, 'class', 4, 4)));
		Assert::false($query->matches(new Token(\T_INTERFACE, 'interface', 4, 4)));

		$multiQuery = (new Query())->valueIs('class', 'interface');
		Assert::true($multiQuery->matches(new Token(\T_CLASS, 'class', 4, 4)));
		Assert::true($multiQuery->matches(new Token(\T_INTERFACE, 'interface', 4, 4)));
		Assert::false($multiQuery->matches(new Token(\T_TRAIT, 'trait', 4, 4)));
	}


	public function testValueNot(): void
	{
		$query = (new Query())->valueNot('class');
		Assert::false($query->matches(new Token(\T_CLASS, 'class', 4, 4)));
		Assert::true($query->matches(new Token(\T_INTERFACE, 'interface', 4, 4)));

		$multiQuery = (new Query())->valueNot('class', 'interface');
		Assert::false($multiQuery->matches(new Token(\T_CLASS, 'class', 4, 4)));
		Assert::false($multiQuery->matches(new Token(\T_INTERFACE, 'interface', 4, 4)));
		Assert::true($multiQuery->matches(new Token(\T_TRAIT, 'trait', 4, 4)));
	}


	public function testValueLike(): void
	{
		$query = (new Query())->valueLike('/\d+\.\d+/');
		Assert::true($query->matches(new Token(\T_DNUMBER, '42.0', 4, 4)));
		Assert::false($query->matches(new Token(\T_LNUMBER, '42', 4, 4)));
	}


	public function testValueNotLike(): void
	{
		$query = (new Query())->valueNotLike('/\d+\.\d+/');
		Assert::false($query->matches(new Token(\T_DNUMBER, '42.0', 4, 4)));
		Assert::true($query->matches(new Token(\T_LNUMBER, '42', 4, 4)));
	}


	public function testIndexIs(): void
	{
		$query = (new Query())->indexIs(4);
		Assert::true($query->matches(new Token(\T_CLASS, 'class', 4, 4)));
		Assert::false($query->matches(new Token(\T_WHITESPACE, ' ', 4, 5)));

		$multiQuery = (new Query())->indexIs(4, 5);
		Assert::true($multiQuery->matches(new Token(\T_CLASS, 'class', 4, 4)));
		Assert::true($multiQuery->matches(new Token(\T_WHITESPACE, ' ', 4, 5)));
		Assert::false($multiQuery->matches(new Token(\T_STRING, 'Foo', 4, 6)));
	}


	public function testIndexNot(): void
	{
		$query = (new Query())->indexNot(4);
		Assert::false($query->matches(new Token(\T_CLASS, 'class', 4, 4)));
		Assert::true($query->matches(new Token(\T_WHITESPACE, ' ', 4, 5)));

		$multiQuery = (new Query())->indexNot(4, 5);
		Assert::false($multiQuery->matches(new Token(\T_CLASS, 'class', 4, 4)));
		Assert::false($multiQuery->matches(new Token(\T_WHITESPACE, ' ', 4, 5)));
		Assert::true($multiQuery->matches(new Token(\T_STRING, 'Foo', 4, 6)));
	}


	public function testIndexGreaterThan(): void
	{
		$query = (new Query())->indexGreaterThan(5);
		Assert::false($query->matches(new Token(\T_CLASS, 'class', 4, 4)));
		Assert::false($query->matches(new Token(\T_WHITESPACE, ' ', 4, 5)));
		Assert::true($query->matches(new Token(\T_STRING, 'Foo', 4, 6)));
	}


	public function testIndexGreaterThanOrEqual(): void
	{
		$query = (new Query())->indexGreaterThanOrEqual(5);
		Assert::false($query->matches(new Token(\T_CLASS, 'class', 4, 4)));
		Assert::true($query->matches(new Token(\T_WHITESPACE, ' ', 4, 5)));
		Assert::true($query->matches(new Token(\T_STRING, 'Foo', 4, 6)));
	}


	public function testIndexLesserThan(): void
	{
		$query = (new Query())->indexLesserThan(5);
		Assert::true($query->matches(new Token(\T_CLASS, 'class', 4, 4)));
		Assert::false($query->matches(new Token(\T_WHITESPACE, ' ', 4, 5)));
		Assert::false($query->matches(new Token(\T_STRING, 'Foo', 4, 6)));
	}


	public function testIndexLesserThanOrEqual(): void
	{
		$query = (new Query())->indexLesserThanOrEqual(5);
		Assert::true($query->matches(new Token(\T_CLASS, 'class', 4, 4)));
		Assert::true($query->matches(new Token(\T_WHITESPACE, ' ', 4, 5)));
		Assert::false($query->matches(new Token(\T_STRING, 'Foo', 4, 6)));
	}


	public function testLineIs(): void
	{
		$query = (new Query())->lineIs(4);
		Assert::true($query->matches(new Token(\T_WHITESPACE, "\n", 4, 5)));
		Assert::false($query->matches(new Token(\T_VARIABLE, '$foo', 5, 6)));
		Assert::false($query->matches(new Token(\T_VARIABLE, '$bar', 6, 13)));

		$query = (new Query())->lineIs(4, 5);
		Assert::true($query->matches(new Token(\T_WHITESPACE, "\n", 4, 5)));
		Assert::true($query->matches(new Token(\T_VARIABLE, '$foo', 5, 6)));
		Assert::false($query->matches(new Token(\T_VARIABLE, '$bar', 6, 13)));
	}


	public function testLineNot(): void
	{
		$query = (new Query())->lineNot(4);
		Assert::false($query->matches(new Token(\T_WHITESPACE, "\n", 4, 5)));
		Assert::true($query->matches(new Token(\T_VARIABLE, '$foo', 5, 6)));
		Assert::true($query->matches(new Token(\T_VARIABLE, '$bar', 6, 13)));

		$query = (new Query())->lineNot(4, 5);
		Assert::false($query->matches(new Token(\T_WHITESPACE, "\n", 4, 5)));
		Assert::false($query->matches(new Token(\T_VARIABLE, '$foo', 5, 6)));
		Assert::true($query->matches(new Token(\T_VARIABLE, '$bar', 6, 13)));
	}


	public function testLineGreaterThan(): void
	{
		$query = (new Query())->lineGreaterThan(5);
		Assert::false($query->matches(new Token(\T_WHITESPACE, "\n", 4, 5)));
		Assert::false($query->matches(new Token(\T_VARIABLE, '$foo', 5, 6)));
		Assert::true($query->matches(new Token(\T_VARIABLE, '$bar', 6, 13)));
	}


	public function testLineGreaterThanOrEqual(): void
	{
		$query = (new Query())->lineGreaterThanOrEqual(5);
		Assert::false($query->matches(new Token(\T_WHITESPACE, "\n", 4, 5)));
		Assert::true($query->matches(new Token(\T_VARIABLE, '$foo', 5, 6)));
		Assert::true($query->matches(new Token(\T_VARIABLE, '$bar', 6, 13)));
	}


	public function testLineLesserThan(): void
	{
		$query = (new Query())->lineLesserThan(5);
		Assert::true($query->matches(new Token(\T_WHITESPACE, "\n", 4, 5)));
		Assert::false($query->matches(new Token(\T_VARIABLE, '$foo', 5, 6)));
		Assert::false($query->matches(new Token(\T_VARIABLE, '$bar', 6, 13)));
	}


	public function testLineLesserThanOrEqual(): void
	{
		$query = (new Query())->lineLesserThanOrEqual(5);
		Assert::true($query->matches(new Token(\T_WHITESPACE, "\n", 4, 5)));
		Assert::true($query->matches(new Token(\T_VARIABLE, '$foo', 5, 6)));
		Assert::false($query->matches(new Token(\T_VARIABLE, '$bar', 6, 13)));
	}


	public function testCustom(): void
	{
		$query = (new Query())->custom(static function (Token $token): bool {
			return $token->getName() === 'T_CLASS';
		});

		Assert::true($query->matches(new Token(\T_CLASS, 'class', 4, 4)));
		Assert::false($query->matches(new Token(\T_INTERFACE, 'interface', 4, 4)));
	}


	public function testMultiple(): void
	{
		$query = (new Query())
			->typeIs(\T_LNUMBER)
			->valueNot('42')
			->lineGreaterThan(4);

		Assert::false($query->matches(new Token(\T_LNUMBER, '48', 3, 3)));
		Assert::false($query->matches(new Token(\T_LNUMBER, '42', 3, 5)));
		Assert::false($query->matches(new Token(\T_LNUMBER, '42', 5, 5)));
		Assert::true($query->matches(new Token(\T_LNUMBER, '48', 5, 5)));
		Assert::false($query->matches(new Token(\T_STRING, '"42"', 8, 8)));
	}

}


(new QueryTest())->run();
