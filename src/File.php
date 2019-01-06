<?php declare(strict_types = 1);

namespace Stylist;

use PhpParser\Node;
use Stylist\Checks\CheckInterface;
use Stylist\IgnoredIssues\IgnoredIssues;
use Stylist\IgnoredIssues\UnmatchedIgnoredIssueCheck;
use Stylist\Tokenista\Tokens;


final class File
{

	/** @var \SplFileInfo */
	private $fileInfo;

	/** @var Tokens */
	private $tokens;

	/** @var Node[] */
	private $statements;

	/** @var Issue[] */
	private $issues = [];

	/** @var string[] */
	private $notes = [];

	/** @var \Throwable|null */
	private $checkError;

	/** @var IgnoredIssues */
	private $ignoredIssues;


	/**
	 * @param Node[] $statements
	 */
	public function __construct(
		\SplFileInfo $fileInfo,
		Tokens $tokens,
		array $statements,
		IgnoredIssues $ignoredIssues
	)
	{
		$this->fileInfo = $fileInfo;
		$this->tokens = $tokens;
		$this->statements = $statements;
		$this->ignoredIssues = $ignoredIssues;
	}


	public function getFileInfo(): \SplFileInfo
	{
		return $this->fileInfo;
	}


	public function getTokens(): Tokens
	{
		return $this->tokens;
	}


	/**
	 * @return Node[]
	 */
	public function getStatements(): array
	{
		return $this->statements;
	}


	public function addIssue(
		CheckInterface $check,
		string $message,
		int $line,
		?callable $fixer = null
	): void
	{
		$issue = new Issue($this, $check, $message, $line, $fixer);
		if ( ! $this->ignoredIssues->isIgnored($issue)) {
			$this->issues[] = $issue;
		}
	}


	public function addNote(string $note): void
	{
		$this->notes[] = $note;
	}


	public function finishedCheck(): void
	{
		// after checking, AST is no longer needed
		// this saves a tremendous amount of memory
		unset($this->statements);

		foreach ($this->ignoredIssues->listUnmatched() as $unmatchedIgnoredIssue) {
			$this->addIssue(
				new UnmatchedIgnoredIssueCheck(),
				\sprintf(
					'%s was expected to report an issue that is configured as ignored, but it did not report any. '
					. 'Remove the issue from the \'ignoredIssues\' configuration if it no longer persists.',
					$unmatchedIgnoredIssue->getCheckName()
				),
				$unmatchedIgnoredIssue->getLine()
			);
		}
	}


	public function isOk(): bool
	{
		return \count($this->issues) === 0
			&& $this->checkError === null;
	}


	public function countIssues(): int
	{
		return \count($this->issues);
	}


	/**
	 * @return Issue[]
	 */
	public function getIssues(): array
	{
		$issues = \array_values($this->issues);
		\usort($issues, static function (Issue $a, Issue $b): int {
			return $a->getLine() <=> $b->getLine();
		});

		return $issues;
	}


	/**
	 * @return string[]
	 */
	public function getNotes(): array
	{
		return $this->notes;
	}


	public function hasCheckError(): bool
	{
		return $this->checkError !== null;
	}


	public function getCheckError(): ?\Throwable
	{
		return $this->checkError;
	}


	public function setCheckError(\Throwable $error): void
	{
		$this->checkError = $error;
	}

}
