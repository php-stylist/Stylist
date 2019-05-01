<?php declare(strict_types = 1);

namespace Stylist\Checks\Classes\NamingConventions;

use PhpParser\Node;
use PhpParser\NodeVisitor;
use Stylist\Checks\AstBasedCheck;
use Stylist\Checks\CallableVisitor;
use Stylist\File;
use Stylist\Utils\NamingConvention;


final class ClassTypeNamingConventionCheck extends AstBasedCheck
{

	/** @var NamingConvention */
	private $namingConvention;


	public function __construct(?NamingConvention $namingConvention = null)
	{
		$this->namingConvention = $namingConvention ?? NamingConvention::pascalCased();
	}


	protected function createVisitor(File $file): NodeVisitor
	{
		return new CallableVisitor(function (Node $node) use ($file): void {
			if (
				$node instanceof Node\Stmt\ClassLike
				&& $node->name !== null
				&& ! $this->namingConvention->matches((string) $node->name)
			) {
				$file->addIssue(
					$this,
					\sprintf(
						'All class type names must be %s, %s found.',
						$this->namingConvention->describe(),
						(string) $node->name
					),
					$node->getLine()
				);
			}
		});
	}

}
