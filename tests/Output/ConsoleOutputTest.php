<?php declare(strict_types = 1);

namespace Stylist\Tests\Output;

use Stylist\CheckResult;
use Stylist\File;
use Stylist\IgnoredIssues\IgnoredIssues;
use Stylist\Output\ConsoleOutput;
use Stylist\Tests\DummyCheck;
use Stylist\Tokenista\Tokens;
use Symfony\Component\Console\Output\StreamOutput;
use Tester\Assert;
use Tester\FileMock;
use Tester\TestCase;


require_once __DIR__ . '/../bootstrap.php';


/**
 * @testCase
 */
final class ConsoleOutputTest extends TestCase
{

	public function testOutput(): void
	{
		$stream = FileMock::create('');
		$resource = \fopen($stream, 'w');
		$symfonyOutput = new StreamOutput($resource);

		$console = new ConsoleOutput('/working/directory', $symfonyOutput);
		$console->initialize(['foo']);

		$files = [];
		$tokens = Tokens::from('<?php');

		$files[] = $file1 = new File(new \SplFileInfo('/working/directory/foo/a'), $tokens, [], new IgnoredIssues([]));
		$console->checkedFile($file1);

		$files[] = $file2 = new File(new \SplFileInfo('/working/directory/foo/b'), $tokens, [], new IgnoredIssues([]));
		$file2->addIssue(new DummyCheck(), 'Error', 5);
		$console->checkedFile($file2);

		$files[] = $file3 = new File(new \SplFileInfo('/working/directory/foo/c'), $tokens, [], new IgnoredIssues([]));
		$file3->addIssue(new DummyCheck(), 'Error 1', 4);
		$file3->addIssue(new DummyCheck(), 'Error 2', 12);
		$console->checkedFile($file3);

		$files[] = $file4 = new File(new \SplFileInfo('/working/directory/foo/d'), $tokens, [], new IgnoredIssues([]));
		$file4->addIssue(new DummyCheck(), 'Error 1', 14);
		$file4->addIssue(new DummyCheck(), 'Error 2', 29);
		$file4->addIssue(new DummyCheck(), 'Error 3', 3);
		$console->checkedFile($file4);

		$files[] = $file5 = new File(new \SplFileInfo('/working/directory/foo/e'), $tokens, [], new IgnoredIssues([]));
		$file5->setCheckError(new \LogicException('Serious error during file check!'));
		$console->checkedFile($file5);

		$files[] = $file6 = new File(new \SplFileInfo('/working/directory/foo/f'), $tokens, [], new IgnoredIssues([]));
		$console->checkedFile($file6);

		$result = new CheckResult(false, $files, 0.5);
		$console->finish($result);

		\fclose($resource);

		Assert::matchFile(
			__DIR__ . '/ConsoleOutputTest.expected.txt',
			\trim(\file_get_contents($stream))
		);
	}

}


(new ConsoleOutputTest())->run();
