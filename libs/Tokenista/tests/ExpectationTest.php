<?php declare(strict_types = 1);

namespace Stylist\Tokenista\Tests;

use Stylist\Tokenista\Expectation;
use Stylist\Tokenista\Query;
use Stylist\Tokenista\Token;
use Stylist\Tokenista\Tokens;
use Tester\Assert;
use Tester\TestCase;


require_once __DIR__ . '/bootstrap.php';


/**
 * @testCase
 */
final class ExpectationTest extends TestCase
{

	public function testConstructor(): void
	{
		$expectation = $this->createExpectation();
		Assert::true($expectation->met());
		Assert::same(0, $expectation->getIndex());
	}


	public function testExpect(): void
	{
		$expectation = $this->createExpectation();

		$openTag = $expectation->expect((new Query())->typeIs(\T_OPEN_TAG));
		Assert::true($expectation->met());
		Assert::same(1, $expectation->getIndex());
		Helpers::assertToken(new Token(\T_OPEN_TAG, "<?php\n", 1, 0), $openTag);

		$variable = $expectation->expect((new Query())->typeIs(\T_VARIABLE));
		Assert::false($expectation->met());
		Assert::same(1, $expectation->getIndex());
		Assert::null($variable);
	}


	public function testMaybe(): void
	{
		$expectation = $this->createExpectation();

		$openTag = $expectation->maybe((new Query())->typeIs(\T_OPEN_TAG));
		Assert::true($expectation->met());
		Assert::same(1, $expectation->getIndex());
		Helpers::assertToken(new Token(\T_OPEN_TAG, "<?php\n", 1, 0), $openTag);

		$variable = $expectation->maybe((new Query())->typeIs(\T_VARIABLE));
		Assert::true($expectation->met());
		Assert::same(1, $expectation->getIndex());
		Assert::null($variable);
	}


	public function testSearch(): void
	{
		$expectation = $this->createExpectation();

		$number = $expectation->search((new Query())->typeIs(\T_LNUMBER));
		Assert::true($expectation->met());
		Assert::same(7, $expectation->getIndex());
		Helpers::assertToken(new Token(T_LNUMBER, '42', 3, 6), $number);

		$interface = $expectation->search((new Query())->typeIs(\T_INTERFACE));
		Assert::false($expectation->met());
		Assert::same(7, $expectation->getIndex());
		Assert::null($interface);
	}


	public function testSection(): void
	{
		$expectation = $this->createExpectation(3, 6);
		$section = $expectation->section((new Query())->valueIs('{'), (new Query())->valueIs('}'));
		Assert::true($expectation->met());
		Assert::same(24, $expectation->getIndex());
		Assert::count(18, $section);

		$expectation2 = $this->createExpectation(1, 14);
		$section2 = $expectation2->section((new Query())->valueIs('<'), (new Query())->valueIs('>'));
		Assert::false($expectation2->met());
		Assert::same(14, $expectation2->getIndex());
		Assert::null($section2);
	}


	public function testWhile(): void
	{
		$expectation = $this->createExpectation();
		$firstThreeLines = $expectation->while((new Query())->lineLesserThan(4));
		Assert::true($expectation->met());
		Assert::same(9, $expectation->getIndex());
		Assert::count(9, $firstThreeLines);

		$expectation2 = $this->createExpectation();
		$everything = $expectation2->while((new Query())->lineGreaterThanOrEqual(1));
		Assert::true($expectation2->met());
		Assert::same(45, $expectation2->getIndex());
		Assert::count(45, $everything);

		$expectation3 = $this->createExpectation();
		$nothing = $expectation3->while((new Query())->lineGreaterThan(5));
		Assert::false($expectation3->met());
		Assert::same(0, $expectation3->getIndex());
		Assert::null($nothing);
	}


	public function testUntil(): void
	{
		$expectation = $this->createExpectation();
		$firstThreeLines = $expectation->until((new Query())->lineIs(4));
		Assert::true($expectation->met());
		Assert::same(9, $expectation->getIndex());
		Assert::count(9, $firstThreeLines);

		$expectation2 = $this->createExpectation();
		$everything = $expectation2->until((new Query())->lineIs(42));
		Assert::true($expectation2->met());
		Assert::same(45, $expectation2->getIndex());
		Assert::count(45, $everything);

		$expectation3 = $this->createExpectation();
		$nothing = $expectation3->until((new Query())->lineIs(1));
		Assert::false($expectation3->met());
		Assert::same(0, $expectation3->getIndex());
		Assert::null($nothing);
	}


	private function createExpectation(int $source = 1, int $index = 0): Expectation
	{
		$rawTokens = require __DIR__ . '/fixtures/source_' . $source . '.php';
		$tokens = new Tokens($rawTokens);
		return new Expectation($tokens, $index);
	}

}


(new ExpectationTest())->run();
