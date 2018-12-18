<?php declare(strict_types = 1);

namespace Stylist\Tests\Fixing;

use Stylist\Fixing\ChangeSet;
use Stylist\Fixing\ConflictingChangesException;
use Stylist\Tokenista\Query;
use Stylist\Tokenista\Tokens;
use Tester\Assert;
use Tester\TestCase;


require_once __DIR__ . '/../bootstrap.php';


/**
 * @testCase
 */
final class ChangeSetTest extends TestCase
{

	public function testChangeSet(): void
	{
		$code = '<?php exit(42);';
		$tokens = Tokens::from($code);
		$exit = $tokens->findOne((new Query())->typeIs(\T_EXIT));
		\assert($exit !== null);

		$changeSet = new ChangeSet();
		$changeSet->replaceToken($exit, 'die');

		$newTokens = $changeSet->apply($tokens);
		Assert::same('<?php die(42);', $newTokens->assemble());
		Assert::same('<?php exit(42);', $tokens->assemble());
	}


	public function testRemoveTokens(): void
	{
		$code = '<?php exit(42);';
		$tokens = Tokens::from($code);
		$parentheses = $tokens->find((new Query())->valueIs('(', ')'));

		$changeSet = new ChangeSet();
		$changeSet->removeTokens($parentheses);

		$newTokens = $changeSet->apply($tokens);
		Assert::same('<?php exit42;', $newTokens->assemble());
		Assert::same('<?php exit(42);', $tokens->assemble());
	}


	public function testReplaceTokens(): void
	{
		$code = '<?php exit(42);';
		$tokens = Tokens::from($code);
		$exit = $tokens->findOne((new Query())->typeIs(\T_EXIT));
		\assert($exit !== null);
		$rightParenthesis = $tokens->findOne((new Query())->valueIs(')'));
		\assert($rightParenthesis !== null);
		$exit42 = $tokens->subset($exit, $rightParenthesis);

		$changeSet = new ChangeSet();
		$changeSet->replaceTokens($exit42, 'true');

		$newTokens = $changeSet->apply($tokens);
		Assert::same('<?php true;', $newTokens->assemble());
		Assert::same('<?php exit(42);', $tokens->assemble());
	}


	public function testMerge(): void
	{
		$code = '<?php exit(42);';
		$tokens = Tokens::from($code);
		$exit = $tokens->findOne((new Query())->typeIs(\T_EXIT));
		\assert($exit !== null);
		$number = $tokens->findOne((new Query())->typeIs(\T_LNUMBER));
		\assert($number !== null);

		$exitChangeSet = new ChangeSet();
		$exitChangeSet->replaceToken($exit, 'die');

		$numberChangeSet = new ChangeSet();
		$numberChangeSet->replaceToken($number, '"hard"');

		$changeSet = new ChangeSet();
		$changeSet->merge($exitChangeSet);
		$changeSet->merge($numberChangeSet);

		$newTokens = $changeSet->apply($tokens);
		Assert::same('<?php die("hard");', $newTokens->assemble());
		Assert::same('<?php exit(42);', $tokens->assemble());
	}


	public function testMergeConflicts(): void
	{
		$code = '<?php exit(42);';
		$tokens = Tokens::from($code);
		$number = $tokens->findOne((new Query())->typeIs(\T_LNUMBER));
		\assert($number !== null);

		$numberChangeSet1 = new ChangeSet();
		$numberChangeSet1->replaceToken($number, '101');

		$numberChangeSet2 = new ChangeSet();
		$numberChangeSet2->removeToken($number);

		$changeSet = new ChangeSet();
		Assert::noError(static function () use ($changeSet, $numberChangeSet1): void {
			$changeSet->merge($numberChangeSet1);
		});

		Assert::throws(static function () use ($changeSet, $numberChangeSet2): void {
			$changeSet->merge($numberChangeSet2);
		}, ConflictingChangesException::class);

		$newTokens = $changeSet->apply($tokens);
		Assert::same('<?php exit(101);', $newTokens->assemble());
		Assert::same('<?php exit(42);', $tokens->assemble());
	}

}


(new ChangeSetTest())->run();
