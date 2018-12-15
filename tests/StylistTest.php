<?php declare(strict_types = 1);

namespace Stylist\Tests;

use PhpParser\Lexer\Emulative;
use PhpParser\Parser\Php7;
use Stylist\CheckResult;
use Stylist\Code\CodeParser;
use Stylist\Code\CodeTokenizer;
use Stylist\File;
use Stylist\FileFactory;
use Stylist\Output\OutputInterface;
use Stylist\Stylist;
use Tester\Assert;
use Tester\TestCase;


require_once __DIR__ . '/bootstrap.php';


/**
 * @testCase
 */
final class StylistTest extends TestCase
{

	public function testChecker(): void
	{
		$output = new class implements OutputInterface {

			public function initialize(array $paths): void
			{
				Assert::same([__DIR__ . '/dummy'], $paths);
			}


			public function checkedFile(File $file): void
			{
				$fileName = $file->getFileInfo()->getFilename();
				if ($fileName === '01_error.phps') {
					Assert::false($file->isOk());
					Assert::same(1, $file->countIssues());
					Assert::false($file->hasCheckError());

					$issue = $file->getIssues()[0];
					Assert::type(DummyCheck::class, $issue->getCheck());
					Assert::same('Error', $issue->getMessage());
					Assert::same(3, $issue->getLine());

				} elseif ($fileName === '02_ok.phps') {
					Assert::true($file->isOk());
					Assert::same(0, $file->countIssues());
					Assert::false($file->hasCheckError());

				} elseif ($fileName === '04_throws.phps') {
					Assert::false($file->isOk());
					Assert::same(0, $file->countIssues());
					Assert::true($file->hasCheckError());

					$error = $file->getCheckError();
					Assert::type(\LogicException::class, $error);
					Assert::same('Serious error in DummyCheck', $error->getMessage());
				}
			}


			public function finish(CheckResult $result): void
			{
			}

		};

		$checks = [new DummyCheck()];
		$parser = new Php7(new Emulative());
		$fileFactory = new FileFactory(new CodeTokenizer(), new CodeParser($parser));

		$stylist = new Stylist($checks, $output, $fileFactory);
		$result = $stylist
			->accept(['*.phps'])
			->exclude(['*excluded*'])
			->check([__DIR__ . '/dummy']);

		Assert::false($result->isSuccess());
		Assert::count(3, $result->getFiles());
	}

}


(new StylistTest())->run();
