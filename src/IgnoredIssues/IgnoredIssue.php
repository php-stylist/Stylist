<?php declare(strict_types = 1);

namespace Stylist\IgnoredIssues;

use Stylist\Issue;
use Webmozart\PathUtil\Path;


final class IgnoredIssue
{

	/** @var string */
	private $checkName;

	/** @var string */
	private $file;

	/** @var int */
	private $line;

	/** @var bool */
	private $matched = false;


	public function __construct(
		string $checkName,
		string $file,
		int $line
	)
	{
		$this->checkName = $checkName;
		$this->file = Path::canonicalize($file);
		$this->line = $line;
	}


	public function matches(Issue $issue): bool
	{
		$matches = $issue->getCheck() instanceof $this->checkName
			&& $this->matchesFile($issue->getFile()->getFileInfo()->getPathname())
			&& $this->line === $issue->getLine();

		if ($matches) {
			$this->matched = true;
		}

		return $matches;
	}


	public function matchesFile(string $fileName): bool
	{
		return Path::canonicalize($fileName) === $this->file;
	}


	public function didMatch(): bool
	{
		return $this->matched;
	}


	public function getCheckName(): string
	{
		return $this->checkName;
	}


	public function getLine(): int
	{
		return $this->line;
	}

}
