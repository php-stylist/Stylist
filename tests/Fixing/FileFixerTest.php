<?php declare(strict_types = 1);

namespace Stylist\Tests\Fixing;

use Stylist\File;
use Stylist\Fixing\ChangeSet;
use Stylist\Fixing\FileFixer;
use Stylist\IgnoredIssues\IgnoredIssues;
use Stylist\Tests\DummyCheck;
use Stylist\Tokenista\Query;
use Stylist\Tokenista\Tokens;
use Tester\Assert;
use Tester\FileMock;
use Tester\TestCase;


require_once __DIR__ . '/../bootstrap.php';


/**
 * @testCase
 */
final class FileFixerTest extends TestCase
{

	public function testFixer(): void
	{
		$code = '<?php exit(42);';
		$tokens = Tokens::from($code);
		$exit = $tokens->findOne((new Query())->typeIs(\T_EXIT));
		\assert($exit !== null);
		$number = $tokens->findOne((new Query())->typeIs(\T_LNUMBER));
		\assert($number !== null);

		$fileMock = FileMock::create($code, 'php');
		$file = new File(
			new \SplFileInfo($fileMock),
			$tokens,
			[],
			new IgnoredIssues([])
		);

		$file->addIssue(
			new DummyCheck(),
			'',
			1,
			static function (ChangeSet $changeSet) use ($exit, $number): void {
				$changeSet->replaceToken($exit, 'die');
				$changeSet->replaceToken($number, '"hard"');
			}
		);

		$fixer = new FileFixer();
		$success = $fixer->fixIssues($file);
		Assert::true($success);
		Assert::true($file->getIssues()[0]->getFix()->isFixed());
		Assert::same(
			'<?php die("hard");',
			\file_get_contents($fileMock)
		);
	}


	public function testConflicts(): void
	{
		$code = '<?php exit(42);';
		$tokens = Tokens::from($code);
		$exit = $tokens->findOne((new Query())->typeIs(\T_EXIT));
		\assert($exit !== null);
		$number = $tokens->findOne((new Query())->typeIs(\T_LNUMBER));
		\assert($number !== null);

		$fileMock = FileMock::create($code, 'php');
		$file = new File(
			new \SplFileInfo($fileMock),
			$tokens,
			[],
			new IgnoredIssues([])
		);

		$file->addIssue(
			new DummyCheck(),
			'',
			1,
			static function (ChangeSet $changeSet) use ($exit): void {
				$changeSet->removeToken($exit);
			}
		);

		$file->addIssue(
			new DummyCheck(),
			'',
			1,
			static function (ChangeSet $changeSet) use ($exit, $number): void {
				$changeSet->replaceToken($exit, 'die');
				$changeSet->replaceToken($number, '"hard"');
			}
		);

		$fixer = new FileFixer();
		$success = $fixer->fixIssues($file);
		Assert::false($success);
		Assert::true($file->getIssues()[0]->getFix()->isFixed());
		Assert::false($file->getIssues()[1]->getFix()->isFixed());
		Assert::same(
			'<?php (42);',
			\file_get_contents($fileMock)
		);
	}

}


(new FileFixerTest())->run();
