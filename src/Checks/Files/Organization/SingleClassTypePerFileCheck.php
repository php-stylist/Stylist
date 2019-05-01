<?php declare(strict_types = 1);

namespace Stylist\Checks\Files\Organization;

use PhpParser\Node;
use PhpParser\NodeVisitor;
use PhpParser\NodeVisitorAbstract;
use Stylist\Checks\AstBasedCheck;
use Stylist\File;


/**
 * Checks that only one type (class, interface or trait) is defined per file.
 *
 * Keeping types in separate files helps better organize the code, and also is
 * necessary in some autoloading schemes, e.g. PSR-4.
 */
final class SingleClassTypePerFileCheck extends AstBasedCheck
{

	protected function createVisitor(File $file): NodeVisitor
	{
		return new class($this, $file) extends NodeVisitorAbstract {

			/** @var SingleClassTypePerFileCheck */
			private $check;

			/** @var File */
			private $file;

			/** @var int */
			private $typesCounter = 0;

			/** @var int */
			private $line;


			public function __construct(
				SingleClassTypePerFileCheck $check,
				File $file
			)
			{
				$this->check = $check;
				$this->file = $file;
			}


			public function enterNode(Node $node)
			{
				if ($node instanceof Node\Stmt\ClassLike && $node->name !== null) {
					$this->typesCounter++;
					$this->line = $node->getLine();
				}
			}


			public function afterTraverse(array $nodes)
			{
				if ($this->typesCounter > 1) {
					$this->file->addIssue(
						$this->check,
						\sprintf(
							'There must be only one type (class, interface or trait) per file, %d found.',
							$this->typesCounter
						),
						$this->line
					);
				}
			}

		};
	}

}
