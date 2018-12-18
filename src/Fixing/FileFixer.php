<?php declare(strict_types = 1);

namespace Stylist\Fixing;

use Stylist\File;
use Stylist\Tokenista\Tokens;


final class FileFixer
{

	/**
	 * @throws CannotWriteFileException
	 */
	public function fixIssues(File $file): bool
	{
		$success = true;
		$changeSet = new ChangeSet();
		foreach ($file->getIssues() as $issue) {
			if ($issue->canBeFixed()) {
				$fix = $issue->getFix();
				\assert($fix !== null);

				$fix->apply($changeSet);
				$success = $fix->isFixed() && $success;
			}
		}

		$tokens = $file->getTokens();
		$newTokens = $changeSet->apply($tokens);

		$this->writeFile($file, $newTokens);
		return $success;
	}


	private function writeFile(File $file, Tokens $tokens): void
	{
		$path = $file->getFileInfo()->getPathname();
		$code = $tokens->assemble();

		if (@\file_put_contents($path, $code) === false) { // @ escalated to exception
			throw new CannotWriteFileException($file);
		}
	}

}
