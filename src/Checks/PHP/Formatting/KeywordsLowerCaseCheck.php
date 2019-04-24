<?php declare(strict_types = 1);

namespace Stylist\Checks\PHP\Formatting;

use Stylist\Checks\CheckInterface;
use Stylist\File;
use Stylist\Fixing\ChangeSet;
use Stylist\Tokenista\Query;


final class KeywordsLowerCaseCheck implements CheckInterface
{

	private static $keywords = [
		\T_ABSTRACT, \T_ARRAY, \T_AS, \T_BREAK, \T_CALLABLE, \T_CASE, \T_CATCH, \T_CLASS, \T_CLONE,
		\T_CONST, \T_CONTINUE, \T_DECLARE, \T_DEFAULT, \T_DO, \T_ECHO, \T_ELSE, \T_ELSEIF, \T_EMPTY,
		\T_ENDDECLARE, \T_ENDFOR, \T_ENDFOREACH, \T_ENDIF, \T_ENDSWITCH, \T_ENDWHILE, \T_EVAL, \T_EXIT,
		\T_EXTENDS, \T_FINAL, \T_FINALLY, \T_FOR, \T_FOREACH, \T_FUNCTION, \T_GLOBAL, \T_GOTO, \T_IF,
		\T_IMPLEMENTS, \T_INCLUDE, \T_INCLUDE_ONCE, \T_INSTANCEOF, \T_INSTEADOF, \T_INTERFACE, \T_ISSET,
		\T_LIST, \T_LOGICAL_AND, \T_LOGICAL_OR, \T_LOGICAL_XOR, \T_NAMESPACE, \T_NEW, \T_PRINT, \T_PRIVATE,
		\T_PROTECTED, \T_PUBLIC, \T_REQUIRE, \T_REQUIRE_ONCE, \T_RETURN, \T_STATIC, \T_SWITCH, \T_THROW,
		\T_TRAIT, \T_TRY, \T_UNSET, \T_USE, \T_VAR, \T_WHILE, \T_YIELD, \T_YIELD_FROM,
	];


	public function check(File $file): void
	{
		$query = (new Query())->typeIs(...self::$keywords);
		$keywordTokens = $file->getTokens()->find($query);
		foreach ($keywordTokens as $keywordToken) {
			$value = $keywordToken->getValue();
			if (\strtolower($value) !== $value) {
				$file->addIssue(
					$this,
					\sprintf('PHP keywords must be lower-cased, %s found.', $value),
					$keywordToken->getLine(),
					static function (ChangeSet $changeSet) use ($keywordToken, $value): void {
						$changeSet->replaceToken($keywordToken, \strtolower($value));
					}
				);
			}
		}
	}

}
