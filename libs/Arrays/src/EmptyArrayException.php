<?php declare(strict_types = 1);

namespace Stylist\Arrays;


final class EmptyArrayException extends \LogicException
{

	public function __construct(string $functionName)
	{
		parent::__construct(\sprintf(
			'Stylist\\Arrays\\%s() cannot be called with an empty array.',
			$functionName
		));
	}

}
