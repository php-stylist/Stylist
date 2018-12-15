<?php declare(strict_types = 1);

namespace Stylist;

use Stylist\Code\CodeParser;
use Stylist\Code\CodeTokenizer;


final class FileFactory
{

	/** @var CodeTokenizer */
	private $codeTokenizer;

	/** @var CodeParser */
	private $codeParser;


	public function __construct(
		CodeTokenizer $codeTokenizer,
		CodeParser $codeParser
	)
	{
		$this->codeTokenizer = $codeTokenizer;
		$this->codeParser = $codeParser;
	}


	public function create(\SplFileInfo $fileInfo): File
	{
		$fileName = $fileInfo->getPathname();
		$code = \file_get_contents($fileName);
		if ($code === false) {
			throw new \RuntimeException(\sprintf(
				'Cannot load contents of "%s".',
				$fileName
			));
		}

		$tokens = $this->codeTokenizer->tokenize($code);
		$statements = $this->codeParser->parse($code);

		return new File(
			$fileInfo,
			$tokens,
			$statements
		);
	}

}
