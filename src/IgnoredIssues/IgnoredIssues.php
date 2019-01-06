<?php declare(strict_types = 1);

namespace Stylist\IgnoredIssues;

use Stylist\Arrays\Arrays;
use Stylist\Issue;


final class IgnoredIssues
{

	/** @var IgnoredIssue[] */
	private $ignoredIssues;


	/**
	 * @param IgnoredIssue[] $ignoredIssues
	 */
	public function __construct(array $ignoredIssues)
	{
		$this->ignoredIssues = $ignoredIssues;
	}


	public function isIgnored(Issue $issue): bool
	{
		foreach ($this->ignoredIssues as $ignoredIssue) {
			if ($ignoredIssue->matches($issue)) {
				return true;
			}
		}

		return false;
	}


	public function forFile(string $fileName): self
	{
		return new self(
			Arrays::filter(
				$this->ignoredIssues,
				static function (IgnoredIssue $ignoredIssue) use ($fileName): bool {
					return $ignoredIssue->matchesFile($fileName);
				}
			)
		);
	}


	/**
	 * @return IgnoredIssue[]
	 */
	public function listUnmatched(): array
	{
		return Arrays::filter(
			$this->ignoredIssues,
			static function (IgnoredIssue $ignoredIssue): bool {
				return ! $ignoredIssue->didMatch();
			}
		);
	}

}
