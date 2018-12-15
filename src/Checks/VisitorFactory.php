<?php declare(strict_types = 1);

namespace Stylist\Checks;

use Nette\StaticClass;
use PhpParser\Node;
use PhpParser\NodeVisitor;
use PhpParser\NodeVisitorAbstract;


final class VisitorFactory
{

	use StaticClass;


	public static function createSimpleVisitor(callable $nodeChecker): NodeVisitor
	{
		return new class($nodeChecker) extends NodeVisitorAbstract {

			/** @var callable */
			private $nodeChecker;


			public function __construct(callable $nodeChecker)
			{
				$this->nodeChecker = $nodeChecker;
			}


			public function enterNode(Node $node): void
			{
				($this->nodeChecker)($node);
			}

		};
	}

}
