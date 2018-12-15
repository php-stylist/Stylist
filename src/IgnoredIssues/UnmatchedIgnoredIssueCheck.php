<?php declare(strict_types = 1);

namespace Stylist\IgnoredIssues;

use Stylist\Checks\CheckInterface;
use Stylist\File;


/**
 * @internal
 */
final class UnmatchedIgnoredIssueCheck implements CheckInterface
{

	public function check(File $file): void
	{
		throw new \LogicException(\sprintf(
			'%s is only a marker class, it cannot be used as a check.',
			self::class
		));
	}

}
