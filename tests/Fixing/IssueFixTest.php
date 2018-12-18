<?php declare(strict_types = 1);

namespace Stylist\Tests\Fixing;

use Stylist\Fixing\ChangeSet;
use Stylist\Fixing\IssueFix;
use Stylist\Tokenista\Token;
use Tester\Assert;
use Tester\TestCase;


require_once __DIR__ . '/../bootstrap.php';


/**
 * @testCase
 */
final class IssueFixTest extends TestCase
{

	public function testFix(): void
	{
		$fix = new IssueFix(static function (ChangeSet $changeSet): void {
			$changeSet->removeToken(Token::literal('test'));
		});

		$changeSet = new ChangeSet();
		Assert::false($fix->isFixed());
		$fix->apply($changeSet);
		Assert::true($fix->isFixed());
	}


	public function testConflict(): void
	{
		$token = Token::literal('test');
		$fix = new IssueFix(static function (ChangeSet $changeSet) use ($token): void {
			$changeSet->removeToken($token);
		});

		$changeSet = new ChangeSet();
		$changeSet->replaceToken($token, 'foo');

		Assert::false($fix->isFixed());
		$fix->apply($changeSet);
		Assert::false($fix->isFixed());
	}

}


(new IssueFixTest())->run();
