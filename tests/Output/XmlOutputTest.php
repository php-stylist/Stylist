<?php declare(strict_types = 1);

namespace Stylist\Tests\Output;

use Stylist\CheckResult;
use Stylist\File;
use Stylist\Output\XmlOutput;
use Stylist\Tests\DummyCheck;
use Stylist\Tokenista\Tokens;
use Tester\Assert;
use Tester\FileMock;
use Tester\TestCase;


require_once __DIR__ . '/../bootstrap.php';


/**
 * @testCase
 */
final class XmlOutputTest extends TestCase
{

	public function testOutput(): void
	{
		$xml = FileMock::create('');

		$xmlOutput = new XmlOutput($xml);
		$xmlOutput->initialize(['foo']);

		$files = [];
		$tokens = Tokens::from('<?php');

		$files[] = $file1 = new File(new \SplFileInfo('foo/a'), $tokens, []);
		$xmlOutput->checkedFile($file1);

		$files[] = $file2 = new File(new \SplFileInfo('foo/b'), $tokens, []);
		$file2->addIssue(new DummyCheck(), 'Error', 5);
		$xmlOutput->checkedFile($file2);

		$files[] = $file3 = new File(new \SplFileInfo('foo/c'), $tokens, []);
		$file3->addIssue(new DummyCheck(), 'Error 1', 4);
		$file3->addIssue(new DummyCheck(), 'Error 2', 12);
		$xmlOutput->checkedFile($file3);

		$files[] = $file4 = new File(new \SplFileInfo('foo/d'), $tokens, []);
		$file4->addIssue(new DummyCheck(), 'Error 1', 14);
		$file4->addIssue(new DummyCheck(), 'Error 2', 29);
		$file4->addIssue(new DummyCheck(), 'Error 3', 3);
		$xmlOutput->checkedFile($file4);

		$files[] = $file5 = new File(new \SplFileInfo('foo/e'), $tokens, []);
		$file5->setCheckError(new \LogicException('Serious error during file check!'));
		$xmlOutput->checkedFile($file5);

		$files[] = $file6 = new File(new \SplFileInfo('foo/f'), $tokens, []);
		$xmlOutput->checkedFile($file6);

		$result = new CheckResult(false, $files, 0.5);
		$xmlOutput->finish($result);

		Assert::same(
			\file_get_contents(__DIR__ . '/XmlOutputTest.expected.xml'),
			\file_get_contents($xml)
		);
	}

}


(new XmlOutputTest())->run();
