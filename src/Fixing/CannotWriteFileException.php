<?php declare(strict_types = 1);

namespace Stylist\Fixing;

use Stylist\File;


final class CannotWriteFileException extends \RuntimeException
{

	public function __construct(File $file)
	{
		parent::__construct(\sprintf(
			'Cannot write file %s, please make sure that you have write permissions to the file.',
			$file->getFileInfo()->getPathname()
		));
	}

}
