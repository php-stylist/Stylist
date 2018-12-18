<?php declare(strict_types = 1);

namespace Stylist\Tests;

use Stylist\Checks\CheckInterface;
use Stylist\File;
use Stylist\Fixing\ChangeSet;
use Stylist\Tokenista\Query;


final class DummyCheck implements CheckInterface
{

	public function check(File $file): void
	{
		$tokens = $file->getTokens();

		$query = (new Query())->typeIs(\T_COMMENT);
		foreach ($tokens->find($query) as $token) {
			switch (\trim($token->getValue())) {
				case '// error':
					$file->addIssue(
						$this,
						'Error',
						$token->getLine(),
						static function (ChangeSet $changeSet): void {}
					);
					return;

				case '// throws':
					throw new \LogicException('Serious error in DummyCheck');

				case '// ok':
				default:
					return;
			}
		}
	}

}
