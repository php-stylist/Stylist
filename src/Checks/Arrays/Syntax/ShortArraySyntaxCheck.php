<?php declare(strict_types = 1);

namespace Stylist\Checks\Arrays\Syntax;

use PhpParser\Node;
use PhpParser\NodeVisitor;
use Stylist\Checks\AstBasedCheck;
use Stylist\Checks\CallableVisitor;
use Stylist\File;
use Stylist\Fixing\ChangeSet;
use Stylist\Tokenista\Query;


final class ShortArraySyntaxCheck extends AstBasedCheck
{

	protected function createVisitor(File $file): NodeVisitor
	{
		return new CallableVisitor(function (Node $node) use ($file) {
			if ($node instanceof Node\Expr\Array_) {
				$firstTokenIndex = (int) $node->getAttribute('startTokenPos');
				$expectation = $file->getTokens()->expect($firstTokenIndex);
				$arrayKeyword = $expectation->expect((new Query())->typeIs(\T_ARRAY));
				$parentheses = $expectation->section((new Query())->valueIs('('), (new Query())->valueIs(')'));

				if ($expectation->met()) {
					\assert($arrayKeyword !== null && $parentheses !== null);
					$file->addIssue(
						$this,
						'Short array syntax must be used, array() found.',
						$arrayKeyword->getLine(),
						static function (ChangeSet $changeSet) use ($arrayKeyword, $parentheses): void {
							$changeSet->removeToken($arrayKeyword);
							$changeSet->replaceToken($parentheses->getFirst(), '[');
							$changeSet->replaceToken($parentheses->getLast(), ']');
						}
					);
				}
			}
		});
	}

}
