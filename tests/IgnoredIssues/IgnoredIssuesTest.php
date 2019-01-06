<?php declare(strict_types = 1);

namespace Stylist\Tests\IgnoredIssues;

use Stylist\File;
use Stylist\IgnoredIssues\IgnoredIssue;
use Stylist\IgnoredIssues\IgnoredIssues;
use Stylist\Issue;
use Stylist\Tests\DummyCheck;
use Stylist\Tokenista\Tokens;
use Tester\Assert;
use Tester\TestCase;


require_once __DIR__ . '/../bootstrap.php';


/**
 * @testCase
 */
final class IgnoredIssuesTest extends TestCase
{

	public function testIgnoredIssues(): void
	{
		$filePath = __DIR__ . '/../dummy/01_error.phps';
		$filePath2 = __DIR__ . '/../dummy/04_throws.phps';
		$ignoredIssues = new IgnoredIssues([
			new IgnoredIssue(DummyCheck::class, $filePath, 3),
			$ignoredIssueForFile = new IgnoredIssue(DummyCheck::class, $filePath2, 5),
		]);

		$file = new File(new \SplFileInfo($filePath), Tokens::from('<?php'), [], new IgnoredIssues([]));
		Assert::false($ignoredIssues->isIgnored(new Issue($file, new DummyCheck(), 'Error', 42)));
		Assert::true($ignoredIssues->isIgnored(new Issue($file, new DummyCheck(), 'Error', 3)));

		$forFile = $ignoredIssues->forFile($filePath2);
		$unmatchedArray = $forFile->listUnmatched();
		Assert::count(1, $unmatchedArray);
		Assert::same($ignoredIssueForFile, $unmatchedArray[0]);
	}

}


(new IgnoredIssuesTest())->run();
