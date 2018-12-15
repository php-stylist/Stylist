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
final class IgnoredIssueTest extends TestCase
{

	public function testIgnoredIssue(): void
	{
		$file = new File(new \SplFileInfo(__FILE__), Tokens::from('<?php'), [], new IgnoredIssues([]));
		$ignoredIssue = new IgnoredIssue(
			DummyCheck::class,
			__FILE__,
			42
		);

		Assert::same(DummyCheck::class, $ignoredIssue->getCheckName());
		Assert::same(42, $ignoredIssue->getLine());
		Assert::false($ignoredIssue->didMatch());

		$nonMatchingIssue = new Issue($file, new DummyCheck(), 'Error', 5);
		Assert::false($ignoredIssue->matches($nonMatchingIssue));
		Assert::false($ignoredIssue->didMatch());

		$matchingIssue = new Issue($file, new DummyCheck(), 'Error', 42);
		Assert::true($ignoredIssue->matches($matchingIssue));
		Assert::true($ignoredIssue->didMatch());
	}

}


(new IgnoredIssueTest())->run();
