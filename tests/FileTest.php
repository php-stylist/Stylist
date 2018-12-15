<?php declare(strict_types = 1);

namespace Stylist\Tests;

use Stylist\File;
use Stylist\IgnoredIssues\IgnoredIssue;
use Stylist\IgnoredIssues\IgnoredIssues;
use Stylist\IgnoredIssues\UnmatchedIgnoredIssueCheck;
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

		$file = new File(new \SplFileInfo(__FILE__), $tokens, [], new IgnoredIssues([]));
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


	public function testIgnoredIssues(): void
	{
		$check = new DummyCheck();
		$tokens = Tokens::from('<?php');

		$unmatchedIgnoredIssue = new IgnoredIssue(DummyCheck::class, __FILE__, 5);
		$matchedIgnoredIssue = new IgnoredIssue(DummyCheck::class, __FILE__, 42);
		$file = new File(
			new \SplFileInfo(__FILE__),
			$tokens,
			[],
			new IgnoredIssues([$unmatchedIgnoredIssue, $matchedIgnoredIssue])
		);

		$file->addIssue($check, 'Error', 42);
		Assert::true($file->isOk());
		Assert::same(0, $file->countIssues());
		Assert::true($matchedIgnoredIssue->didMatch());
		Assert::false($unmatchedIgnoredIssue->didMatch());

		$file->finishedCheck();
		Assert::false($file->isOk());
		Assert::same(1, $file->countIssues());

		$issue = $file->getIssues()[0];
		Assert::type(Issue::class, $issue);
		Assert::type(UnmatchedIgnoredIssueCheck::class, $issue->getCheck());
		$expectedMessage = \sprintf(
			'%s was expected to report an issue that is configured as ignored, but it did not report any. '
			. 'Remove the issue from the \'ignoredIssues\' configuration if it no longer persists.',
			DummyCheck::class
		);
		Assert::same($expectedMessage, $issue->getMessage());
		Assert::same(5, $issue->getLine());
	}

}


(new FileTest())->run();
