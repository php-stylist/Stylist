<?php declare(strict_types = 1);

namespace Stylist\Checks\Files\Whitespace;

use Stylist\Checks\CheckInterface;
use Stylist\File;
use Stylist\Fixing\ChangeSet;
use Stylist\Tokenista\Query;


final class NoTrailingWhitespaceCheck implements CheckInterface
{

	public function check(File $file): void
	{
		$query = (new Query())
			->typeIs(\T_WHITESPACE)
			->valueLike('/([ \t]+)[\r\n]$/');

		foreach ($file->getTokens()->find($query) as $token) {
			$file->addIssue(
				$this,
				'Trailing whitespace found. There should be none.',
				$token->getLine(),
				static function (ChangeSet $changeSet) use ($token): void {
					$changeSet->replaceToken(
						$token,
						\ltrim($token->getValue(), "\t ")
					);
				}
			);
		}
	}

}
