<?php declare(strict_types = 1);

namespace Stylist\Checks\Files\Language;

use Stylist\Checks\CheckInterface;
use Stylist\File;
use Stylist\Fixing\ChangeSet;
use Stylist\Tokenista\Query;


final class NoCloseTagCheck implements CheckInterface
{

	public function check(File $file): void
	{
		$query = (new Query())->typeIs(\T_CLOSE_TAG);
		foreach ($file->getTokens()->find($query) as $closeTagToken) {
			$file->addIssue(
				$this,
				'Close tag found. Files containing only PHP code must not contain the PHP close tag.',
				$closeTagToken->getLine(),
				static function (ChangeSet $changeSet) use ($closeTagToken): void {
					$changeSet->removeToken($closeTagToken);
				}
			);
		}
	}

}
