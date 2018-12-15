<?php declare(strict_types = 1);

namespace Stylist\Tests\Checks;

use PhpParser\Lexer;
use PhpParser\ParserFactory;
use Stylist\Checks\CheckInterface;
use Stylist\Code\CodeParser;
use Stylist\Code\CodeTokenizer;
use Stylist\File;
use Stylist\FileFactory;
use Tester\Assert;
use Tester\TestCase;


abstract class CheckTestCase extends TestCase
{

	/** @var FileFactory */
	private $fileFactory;


	protected function setUp(): void
	{
		$phpParser = (new ParserFactory())->create(
			ParserFactory::PREFER_PHP7,
			new Lexer([
				'usedAttributes' => [
					'comments',
					'startLine',
					'endLine',
					'startTokenPos',
					'endTokenPos',
				],
			])
		);

		$this->fileFactory = new FileFactory(
			new CodeTokenizer(),
			new CodeParser($phpParser)
		);
	}


	protected function assertFile(
		CheckInterface $check,
		string $fileName,
		array $expectedIssues
	): void
	{
		$file = $this->checkFile($check, $fileName);
		Assert::same(\count($expectedIssues), $file->countIssues());

		foreach ($expectedIssues as $index => $expectedIssue) {
			[$expectedLine, $expectedMessage] = $expectedIssue;

			$issue = $file->getIssues()[$index];
			Assert::type($check, $issue->getCheck());
			Assert::same($expectedLine, $issue->getLine());
			Assert::same($expectedMessage, $issue->getMessage());
		}
	}


	private function checkFile(CheckInterface $check, string $fileName): File
	{
		$fileInfo = new \SplFileInfo($fileName);
		$file = $this->fileFactory->create($fileInfo);

		$check->check($file);
		return $file;
	}

}
