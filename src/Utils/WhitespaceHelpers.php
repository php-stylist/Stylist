<?php declare(strict_types = 1);

namespace Stylist\Utils;

use Nette\Utils\Strings;
use Stylist\Tokenista\Query;
use Stylist\Tokenista\Token;
use Stylist\Tokenista\Tokens;


final class WhitespaceHelpers
{

	public const INDENTATION_PATTERN = '/\n([ \t]+)$/';


	private function __construct()
	{
	}


	public static function countNewLines(string $whitespace): int
	{
		return \substr_count($whitespace, "\n");
	}


	public static function getLineIndentation(Tokens $tokens, int $line): string
	{
		$currentIndentation = $tokens->findOne((new Query())
			->typeIs(\T_WHITESPACE)
			->lineIs($line - 1)
			->valueLike(self::INDENTATION_PATTERN)
		);
		\assert($currentIndentation !== null);

		return self::extractIndentation($currentIndentation);
	}


	public static function extractIndentation(Token $token): string
	{
		[, $indentation] = Strings::match($token->getValue(), self::INDENTATION_PATTERN);
		return $indentation;
	}

}
