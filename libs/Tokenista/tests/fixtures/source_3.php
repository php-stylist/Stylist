<?php declare(strict_types = 1);

use Stylist\Tokenista\Token;


// valid PHP code with reserved word in a method name

return [
	new Token(T_OPEN_TAG, "<?php\n", 1, 0),
	new Token(T_WHITESPACE, "\n", 2, 1),
	new Token(T_CLASS, 'class', 3, 2),
	new Token(T_WHITESPACE, ' ', 3, 3),
	new Token(T_STRING, 'Foo', 3, 4),
	new Token(T_WHITESPACE, ' ', 3, 5),
	new Token(Token::T_UNKNOWN, '{', 3, 6),
	new Token(T_WHITESPACE, "\n\t", 3, 7),
	new Token(T_PUBLIC, 'public', 4, 8),
	new Token(T_WHITESPACE, ' ', 4, 9),
	new Token(T_FUNCTION, 'function', 4, 10),
	new Token(T_WHITESPACE, ' ', 4, 11),
	new Token(T_STRING, 'forEach', 4, 12),
	new Token(Token::T_UNKNOWN, '(', 4, 13),
	new Token(Token::T_UNKNOWN, ')', 4, 14),
	new Token(Token::T_UNKNOWN, ':', 4, 15),
	new Token(T_WHITESPACE, ' ', 4, 16),
	new Token(T_STRING, 'void', 4, 17),
	new Token(T_WHITESPACE, ' ', 4, 18),
	new Token(Token::T_UNKNOWN, '{', 4, 19),
	new Token(T_WHITESPACE, "\n\t", 4, 20),
	new Token(Token::T_UNKNOWN, '}', 5, 21),
	new Token(T_WHITESPACE, "\n", 5, 22),
	new Token(Token::T_UNKNOWN, '}', 6, 23),
	new Token(T_WHITESPACE, "\n", 6, 24),
];
