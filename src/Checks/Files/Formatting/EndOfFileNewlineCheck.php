<?php declare(strict_types = 1);

namespace Stylist\Checks\Files\Formatting;

use Stylist\Checks\CheckInterface;
use Stylist\File;
use Stylist\Fixing\ChangeSet;


final class EndOfFileNewlineCheck implements CheckInterface
{

	public function check(File $file): void
	{
		$lastToken = $file->getTokens()->getLast();
		$isWhitespace = $lastToken->getType() === \T_WHITESPACE;
		$endsWithNewLine = \substr($lastToken->getValue(), -1) === "\n";

		if ( ! ($isWhitespace && $endsWithNewLine)) {
			$file->addIssue(
				$this,
				'File must end with a newline character.',
				$lastToken->getLine(),
				static function (ChangeSet $changeSet) use ($lastToken): void {
					$changeSet->appendTo($lastToken, "\n");
				}
			);
		}
	}

}
