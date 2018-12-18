<?php declare(strict_types = 1);

namespace Stylist\Checks\Arrays;

use PhpParser\Node;
use PhpParser\NodeVisitor;
use Stylist\Checks\AbstractCheck;
use Stylist\Checks\VisitorFactory;
use Stylist\File;
use Stylist\Fixing\ChangeSet;
use Stylist\Tokenista\Query;


final class ShortArraySyntaxCheck extends AbstractCheck
{

	protected function createVisitor(File $file): NodeVisitor
	{
		return VisitorFactory::createSimpleVisitor(function (Node $node) use ($file): void {
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
