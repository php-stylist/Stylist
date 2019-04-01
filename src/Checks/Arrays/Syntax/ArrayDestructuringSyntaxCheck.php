<?php declare(strict_types = 1);

namespace Stylist\Checks\Arrays\Syntax;

use PhpParser\Node;
use PhpParser\NodeVisitor;
use Stylist\Checks\AbstractCheck;
use Stylist\Checks\CallableVisitor;
use Stylist\File;
use Stylist\Fixing\ChangeSet;
use Stylist\Tokenista\Query;


/**
 * Checks that array destructuring expressions follow given syntax.
 */
final class ArrayDestructuringSyntaxCheck extends AbstractCheck
{

	public const SHORT = 'short';
	public const LONG = 'long';


	/** @var string */
	private $style;


	private function __construct(string $style = self::SHORT)
	{
		$this->style = $style;
	}


	/**
	 * This setting enforces the short syntax ('[]') introduced in PHP 7.1.
	 *
	 * ```incorrect
	 * list($a,, $c) = $array;
	 * ```
	 *
	 * ```correct
	 * [$a,, $c) = $array;
	 * ```
	 */
	public static function short(): self
	{
		return new self(self::SHORT);
	}


	/**
	 * This setting enforces the longer `list()` syntax.
	 *
	 * ```incorrect
	 * [$a,, $c) = $array;
	 * ```
	 *
	 * ```correct
	 * list($a,, $c) = $array;
	 * ```
	 */
	public static function long(): self
	{
		return new self(self::LONG);
	}


	protected function createVisitor(File $file): NodeVisitor
	{
		return new CallableVisitor(function (Node $node) use ($file) {
			if ($this->style === self::SHORT && $node instanceof Node\Expr\List_) {
				$firstTokenIndex = (int) $node->getAttribute('startTokenPos');
				$expectation = $file->getTokens()->expect($firstTokenIndex);
				$list = $expectation->expect((new Query())->typeIs(\T_LIST));
				$parentheses = $expectation->section((new Query())->valueIs('('), (new Query())->valueIs(')'));

				if ( ! $expectation->met()) {
					return null;
				}

				\assert($list !== null && $parentheses !== null);
				$file->addIssue(
					$this,
					'Short array destructuring syntax must be used, list() found.',
					$node->getLine(),
					static function (ChangeSet $changeSet) use ($list, $parentheses): void {
						$changeSet->removeToken($list);
						$changeSet->replaceToken($parentheses->getFirst(), '[');
						$changeSet->replaceToken($parentheses->getLast(), ']');
					}
				);

			} elseif ($this->style === self::LONG) {
				if ($node instanceof Node\Expr\Assign && $node->var instanceof Node\Expr\Array_) {
					$arrayDestructuring = $node->var;

				} elseif ($node instanceof Node\Stmt\Foreach_ && $node->valueVar instanceof Node\Expr\Array_) {
					$arrayDestructuring = $node->valueVar;

				} else {
					return null;
				}

				$tokens = $file->getTokens();
				$leftBracketIndex = $arrayDestructuring->getAttribute('startTokenPos');
				$leftBracket = $tokens[$leftBracketIndex];
				$rightBracketIndex = $arrayDestructuring->getAttribute('endTokenPos');
				$rightBracket = $tokens[$rightBracketIndex];
				\assert($leftBracket !== null && $rightBracket !== null);

				$file->addIssue(
					$this,
					'list() must be used for array destructuring, short syntax found.',
					$arrayDestructuring->getLine(),
					static function (ChangeSet $changeSet) use ($leftBracket, $rightBracket): void {
						$changeSet->replaceToken($leftBracket, 'list(');
						$changeSet->replaceToken($rightBracket, ')');
					}
				);
			}
		});
	}

}
