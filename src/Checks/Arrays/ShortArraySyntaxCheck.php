<?php declare(strict_types = 1);

namespace Stylist\Checks\Arrays;

use PhpParser\Node;
use PhpParser\NodeVisitor;
use Stylist\Checks\AbstractCheck;
use Stylist\Checks\VisitorFactory;
use Stylist\File;
use Stylist\Tokenista\Token;


final class ShortArraySyntaxCheck extends AbstractCheck
{

	protected function createVisitor(File $file): NodeVisitor
	{
		return VisitorFactory::createSimpleVisitor(function (Node $node) use ($file): void {
			if ($node instanceof Node\Expr\Array_) {
				$firstTokenIndex = $node->getAttribute('startTokenPos');
				$firstToken = $file->getTokens()[$firstTokenIndex];
				\assert($firstToken instanceof Token);

				if ($firstToken->getType() === \T_ARRAY) {
					$file->addIssue(
						$this,
						'Short array syntax must be used, array() found.',
						$firstToken->getLine()
					);
				}
			}
		});
	}

}
