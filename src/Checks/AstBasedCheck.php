<?php declare(strict_types = 1);

namespace Stylist\Checks;

use PhpParser\Node;
use PhpParser\NodeTraverser;
use PhpParser\NodeVisitor;
use PhpParser\NodeVisitor\NameResolver;
use Stylist\File;
use Stylist\Tokenista\Tokens;


abstract class AstBasedCheck implements CheckInterface
{

	public function check(File $file): void
	{
		$visitor = $this->createVisitor($file);

		$traverser = new NodeTraverser();
		$traverser->addVisitor(new NameResolver(null, ['preserveOriginalNames' => true]));
		$traverser->addVisitor($visitor);

		$traverser->traverse($file->getStatements());
	}


	protected function extractNodeTokens(File $file, Node $node): Tokens
	{
		$tokens = $file->getTokens();

		$firstTokenIndex = (int) $node->getAttribute('startTokenPos');
		$firstToken = $tokens[$firstTokenIndex];
		\assert($firstToken !== null);

		$lastTokenIndex = (int) $node->getAttribute('endTokenPos');
		$lastToken = $tokens[$lastTokenIndex];
		\assert($lastToken !== null);

		return $tokens->subset($firstToken, $lastToken);
	}


	abstract protected function createVisitor(File $file): NodeVisitor;

}
