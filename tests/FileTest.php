<?php declare(strict_types = 1);

namespace Stylist\Tests;

use Stylist\File;
use Stylist\Issue;
use Stylist\Tokenista\Tokens;
use Tester\Assert;
use Tester\TestCase;


require_once __DIR__ . '/bootstrap.php';


/**
 * @testCase
 */
final class FileTest extends TestCase
{

	public function testIssues(): void
	{
		$check = new DummyCheck();
		$tokens = Tokens::from('<?php');

		$file = new File(new \SplFileInfo(__FILE__), $tokens, []);
		Assert::true($file->isOk());
		Assert::same(0, $file->countIssues());

		$file->addIssue($check, 'Error', 42);
		Assert::false($file->isOk());
		Assert::same(1, $file->countIssues());

		$issue = $file->getIssues()[0];
		Assert::type(Issue::class, $issue);
		Assert::same($check, $issue->getCheck());
		Assert::same('Error', $issue->getMessage());
		Assert::same(42, $issue->getLine());
	}

}


(new FileTest())->run();
