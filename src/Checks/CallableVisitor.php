<?php declare(strict_types = 1);

namespace Stylist\Checks;

use PhpParser\Node;
use PhpParser\NodeVisitorAbstract;


final class CallableVisitor extends NodeVisitorAbstract
{

	/** @var callable */
	private $callable;


	public function __construct(callable $callable)
	{
		$this->callable = $callable;
	}


	public function enterNode(Node $node)
	{
		return ($this->callable)($node);
	}

}
