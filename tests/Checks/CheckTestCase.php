<?php declare(strict_types = 1);

namespace Stylist\Tests\Checks;

use PhpParser\Lexer;
use PhpParser\ParserFactory;
use Stylist\Checks\CheckInterface;
use Stylist\Code\CodeParser;
use Stylist\Code\CodeTokenizer;
use Stylist\File;
use Stylist\FileFactory;
use Stylist\Fixing\ChangeSet;
use Stylist\IgnoredIssues\IgnoredIssues;
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
			new CodeParser($phpParser),
			new IgnoredIssues([])
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

		foreach ($expectedIssues as $index => [$expectedLine, $expectedMessage]) {
			$issue = $file->getIssues()[$index];
			Assert::type($check, $issue->getCheck());
			Assert::same($expectedLine, $issue->getLine());
			Assert::same($expectedMessage, $issue->getMessage());
		}
	}


	protected function assertFixed(CheckInterface $check, string $fileName, string $expectedFile): void
	{
		$file = $this->checkFile($check, $fileName);
		$changeSet = new ChangeSet();

		foreach ($file->getIssues() as $issue) {
			if ($issue->canBeFixed()) {
				$fix = $issue->getFix();
				\assert($fix !== null);

				$fix->apply($changeSet);
			}
		}

		$tokens = $file->getTokens();
		$newTokens = $changeSet->apply($tokens);
		Assert::same(
			\file_get_contents($expectedFile),
			$newTokens->assemble()
		);
	}


	private function checkFile(CheckInterface $check, string $fileName): File
	{
		$fileInfo = new \SplFileInfo($fileName);
		$file = $this->fileFactory->create($fileInfo);

		$check->check($file);
		return $file;
	}

}
