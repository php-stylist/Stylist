<?php declare(strict_types = 1);

namespace Stylist\Code;

use Stylist\Tokenista\Tokens;


final class CodeTokenizer
{

	public function tokenize(string $code): Tokens
	{
		return Tokens::from($code);
	}

}
