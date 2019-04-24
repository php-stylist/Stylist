<?php declare(strict_types = 1);

namespace Stylist\Checks\Files\Language;

use Stylist\Checks\CheckInterface;
use Stylist\File;
use Stylist\Tokenista\Query;


final class PhpOnlyCheck implements CheckInterface
{

	public function check(File $file): void
	{
		$query = (new Query())->typeIs(\T_INLINE_HTML);
		foreach ($file->getTokens()->find($query) as $token) {
			$file->addIssue(
				$this,
				'File must contain only PHP code, inline HTML found.',
				$token->getLine()
			);
		}
	}

}
