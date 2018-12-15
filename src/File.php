<?php declare(strict_types = 1);

namespace Stylist;

use PhpParser\Node;
use Stylist\Checks\CheckInterface;
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

	/** @var \Throwable|null */
	private $checkError;


	/**
	 * @param Node[] $statements
	 */
	public function __construct(
		\SplFileInfo $fileInfo,
		Tokens $tokens,
		array $statements
	)
	{
		$this->fileInfo = $fileInfo;
		$this->tokens = $tokens;
		$this->statements = $statements;
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
		int $line
	): void
	{
		$this->issues[] = new Issue($this, $check, $message, $line);
	}


	public function finishedCheck(): void
	{
		// after checking, AST is no longer needed
		// this saves a tremendous amount of memory
		unset($this->statements);
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
