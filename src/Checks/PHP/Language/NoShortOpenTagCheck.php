<?php declare(strict_types = 1);

namespace Stylist\Checks\PHP\Language;

use Nette\Utils\Strings;
use Stylist\Checks\CheckInterface;
use Stylist\File;
use Stylist\Fixing\ChangeSet;
use Stylist\Tokenista\Query;


final class NoShortOpenTagCheck implements CheckInterface
{

	public function check(File $file): void
	{
		$openTags = (new Query())->typeIs(\T_OPEN_TAG);
		foreach ($file->getTokens()->find($openTags) as $openTagToken) {
			// T_OPEN_TAG contains trailing whitespace
			if ( ! Strings::startsWith($openTagToken->getValue(), '<?php')) {
				$file->addIssue(
					$this,
					'File must not use short PHP open tag, <? found.',
					$openTagToken->getLine(),
					static function (ChangeSet $changeSet) use ($openTagToken): void {
						$changeSet->replaceToken(
							$openTagToken,
							\str_replace('<?', '<?php', $openTagToken->getValue())
						);
					}
				);
			}
		}
	}

}
