<?php declare(strict_types = 1);

namespace Stylist\Checks\Files\Formatting;

use Stylist\Checks\CheckInterface;
use Stylist\File;


/**
 * Checks that lines do not exceed given length limit.
 *
 * Long lines make readability more difficult, especially on handheld devices or laptops.
 * Also, a line too long might indicate a code smell.
 */
final class LineLengthCheck implements CheckInterface
{

	/** @var int */
	private $limit;

	/** @var int */
	private $tabWidth;


	/**
	 * @param int $limit The line length limit, defaults to 120 characters.
	 * @param int $tabWidth Width of a single tab character, defaults to 4.
	 */
	public function __construct(
		int $limit = 120,
		int $tabWidth = 4
	)
	{
		$this->limit = $limit;
		$this->tabWidth = $tabWidth;
	}


	public function check(File $file): void
	{
		$lines = \file($file->getFileInfo()->getPathname(), \FILE_IGNORE_NEW_LINES);
		\assert($lines !== false);

		foreach ($lines as $lineNumber => $line) {
			$line = \str_replace("\t", \str_repeat(' ', $this->tabWidth), $line);
			$length = \function_exists('\\mb_strlen') ? \mb_strlen($line) : \strlen($line);

			if ($length > $this->limit) {
				$file->addIssue(
					$this,
					\sprintf(
						'Line must not be longer than %d characters, %d found.',
						$this->limit,
						$length
					),
					$lineNumber + 1
				);
			}
		}
	}

}
