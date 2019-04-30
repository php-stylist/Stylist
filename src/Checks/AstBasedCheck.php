<?php declare(strict_types = 1);

namespace Stylist\Checks;

use PhpParser\NodeTraverser;
use PhpParser\NodeVisitor;
use PhpParser\NodeVisitor\NameResolver;
use Stylist\File;


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


	abstract protected function createVisitor(File $file): NodeVisitor;

}
