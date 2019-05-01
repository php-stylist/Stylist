<?php declare(strict_types = 1);

namespace Stylist\Checks\Classes\Syntax;

use PhpParser\Node;
use PhpParser\NodeVisitor;
use Stylist\Checks\AstBasedCheck;
use Stylist\Checks\CallableVisitor;
use Stylist\File;
use Stylist\Fixing\ChangeSet;
use Stylist\Tokenista\Query;


final class ClassInstantiationParenthesesCheck extends AstBasedCheck
{

	private const ENFORCE = true;
	private const DISALLOW = false;

	/** @var bool */
	private $mode;


	private function __construct(bool $mode)
	{
		$this->mode = $mode;
	}


	/**
	 * Enforces parentheses even if the argument list is empty.
	 */
	public static function enforce(): self
	{
		return new self(self::ENFORCE);
	}


	/**
	 * Disallows parentheses when possible, i.e. if the argument list is empty.
	 */
	public static function disallow(): self
	{
		return new self(self::DISALLOW);
	}


	protected function createVisitor(File $file): NodeVisitor
	{
		return new CallableVisitor(function (Node $node) use ($file) {
			// ignore anonymous classes
			if ( ! $node instanceof Node\Expr\New_ || $node->class instanceof Node\Stmt\Class_) {
				return null;
			}

			if ($this->mode === self::ENFORCE) {
				$this->enforceParentheses($file, $node);

			} else {
				$this->disallowParentheses($file, $node);
			}
		});
	}


	private function enforceParentheses(File $file, Node\Expr\New_ $node): void
	{
		$lastTokenIndex = (int) $node->getAttribute('endTokenPos');
		$lastToken = $file->getTokens()[$lastTokenIndex];
		\assert($lastToken !== null);

		if (\count($node->args) === 0 && $lastToken->getValue() !== ')') {
			$file->addIssue(
				$this,
				'Class must always be instantiated with parentheses.',
				$node->getLine(),
				static function (ChangeSet $changeSet) use ($lastToken): void {
					$changeSet->appendTo($lastToken, '()');
				}
			);
		}
	}


	private function disallowParentheses(File $file, Node\Expr\New_ $node): void
	{
		if (\count($node->args) > 0) {
			return;
		}

		$tokens = $this->extractNodeTokens($file, $node);
		$expectation = $tokens->expect();
		$expectation->until((new Query())->valueIs('('));
		$leftParenthesis = $expectation->expect((new Query())->valueIs('('));
		$rightParenthesis = $expectation->expect((new Query())->valueIs(')'));

		if ($expectation->met()) {
			$file->addIssue(
				$this,
				'Class must be instantiated without parentheses if possible.',
				$node->getLine(),
				static function (ChangeSet $changeSet) use ($leftParenthesis, $rightParenthesis): void {
					\assert($leftParenthesis !== null && $rightParenthesis !== null);
					$changeSet->removeToken($leftParenthesis);
					$changeSet->removeToken($rightParenthesis);
				}
			);
		}
	}

}
