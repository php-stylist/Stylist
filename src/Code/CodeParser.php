<?php declare(strict_types = 1);

namespace Stylist\Code;

use PhpParser\Error;
use PhpParser\Lexer;
use PhpParser\Node;
use PhpParser\ParserFactory;


final class CodeParser
{

	/** @var \PhpParser\Parser */
	private $parser;


	public function __construct(\PhpParser\Parser $parser)
	{
		$this->parser = $parser;
	}


	/**
	 * @return Node[]
	 * @throws Error
	 */
	public function parse(string $code): array
	{
		$statements = $this->parser->parse($code);
		\assert($statements !== null);

		return $statements;
	}

}
