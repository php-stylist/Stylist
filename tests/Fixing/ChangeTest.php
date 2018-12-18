<?php declare(strict_types = 1);

namespace Stylist\Tests\Fixing;

use Stylist\Fixing\Change;
use Stylist\Tokenista\Query;
use Stylist\Tokenista\Tokens;
use Tester\Assert;
use Tester\TestCase;


require_once __DIR__ . '/../bootstrap.php';


/**
 * @testCase
 */
final class ChangeTest extends TestCase
{

	public function testRemove(): void
	{
		$code = '<?php exit(42);';
		$tokens = Tokens::from($code);
		$exit = $tokens->findOne((new Query())->typeIs(\T_EXIT));
		\assert($exit !== null);

		$change = Change::remove($exit);
		$newTokens = $change->apply($tokens);
		Assert::same('<?php (42);', $newTokens->assemble());
		Assert::same('<?php exit(42);', $tokens->assemble());
	}


	public function testReplace(): void
	{
		$code = '<?php exit(42);';
		$tokens = Tokens::from($code);
		$exit = $tokens->findOne((new Query())->typeIs(\T_EXIT));
		\assert($exit !== null);

		$change = Change::replace($exit, 'die');
		$newTokens = $change->apply($tokens);
		Assert::same('<?php die(42);', $newTokens->assemble());
		Assert::same('<?php exit(42);', $tokens->assemble());
	}


	public function testPrependTo(): void
	{
		$code = '<?php exit(42);';
		$tokens = Tokens::from($code);
		$exit = $tokens->findOne((new Query())->typeIs(\T_EXIT));
		\assert($exit !== null);

		$change = Change::prependTo($exit, 'emergency ');
		$newTokens = $change->apply($tokens);
		Assert::same('<?php emergency exit(42);', $newTokens->assemble());
		Assert::same('<?php exit(42);', $tokens->assemble());
	}


	public function testAppendTo(): void
	{
		$code = '<?php exit(42);';
		$tokens = Tokens::from($code);
		$exit = $tokens->findOne((new Query())->typeIs(\T_EXIT));
		\assert($exit !== null);

		$change = Change::appendTo($exit, ' now');
		$newTokens = $change->apply($tokens);
		Assert::same('<?php exit now(42);', $newTokens->assemble());
		Assert::same('<?php exit(42);', $tokens->assemble());
	}


	public function testConflicts(): void
	{
		$code = '<?php exit(42);';
		$tokens = Tokens::from($code);
		$exit = $tokens->findOne((new Query())->typeIs(\T_EXIT));
		\assert($exit !== null);

		$number = $tokens->findOne((new Query())->typeIs(\T_LNUMBER));
		\assert($number !== null);

		$change1 = Change::remove($exit);
		$change2 = Change::replace($exit, 'die');
		Assert::true($change1->conflictsWith($change2));
		Assert::true($change2->conflictsWith($change1));

		$change3 = Change::replace($number, '42.0');
		Assert::false($change1->conflictsWith($change3));
		Assert::false($change3->conflictsWith($change1));
		Assert::false($change2->conflictsWith($change3));
		Assert::false($change3->conflictsWith($change2));
	}

}


(new ChangeTest())->run();
