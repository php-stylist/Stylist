<?php declare(strict_types = 1);

namespace Stylist\Checks\PHP\Formatting;

use PhpParser\Node;
use PhpParser\NodeVisitor;
use Stylist\Checks\AstBasedCheck;
use Stylist\Checks\CallableVisitor;
use Stylist\File;
use Stylist\Fixing\ChangeSet;


final class TrueFalseNullCaseCheck extends AstBasedCheck
{

	private const LOWER = 'lower';
	private const UPPER = 'upper';

	/** @var string */
	private $case;


	private function __construct(string $case)
	{
		$this->case = $case;
	}


	public static function lower(): self
	{
		return new self(self::LOWER);
	}


	public static function upper(): self
	{
		return new self(self::UPPER);
	}


	protected function createVisitor(File $file): NodeVisitor
	{
		return new CallableVisitor(function (Node $node) use ($file) {
			if (
				$node instanceof Node\Expr\ConstFetch
				&& \in_array(\strtolower((string) $node->name), ['true', 'false', 'null'], true)
			) {
				$this->checkCase($file, $node);
			}
		});
	}


	private function checkCase(File $file, Node\Expr\ConstFetch $node): void
	{
		$name = (string) $node->name;
		$token = $file->getTokens()[(int) $node->getAttribute('startTokenPos')];
		\assert($token !== null);

		if ($this->case === self::LOWER && \strtolower($name) !== $name) {
			$file->addIssue(
				$this,
				\sprintf('PHP constants (true, false, null) must be lower-cased, %s found.', $name),
				$node->getLine(),
				static function (ChangeSet $changeSet) use ($token, $name): void {
					$changeSet->replaceToken($token, \strtolower($name));
				}
			);

		} elseif ($this->case === self::UPPER && \strtoupper($name) !== $name) {
			$file->addIssue(
				$this,
				\sprintf('PHP constants (TRUE, FALSE, NULL) must be upper-cased, %s found.', $name),
				$node->getLine(),
				static function (ChangeSet $changeSet) use ($token, $name): void {
					$changeSet->replaceToken($token, \strtoupper($name));
				}
			);
		}
	}

}
