<?php declare(strict_types = 1);

namespace Stylist\Fixing;


final class ConflictingChangesException extends \RuntimeException
{

	/** @var Change */
	private $firstChange;

	/** @var Change */
	private $secondChange;


	public static function fromChanges(Change $firstChange, Change $secondChange): self
	{
		$self = new self('Conflicting changes detected.');
		$self->firstChange = $firstChange;
		$self->secondChange = $secondChange;

		return $self;
	}


	public function getFirstChange(): Change
	{
		return $this->firstChange;
	}


	public function getSecondChange(): Change
	{
		return $this->secondChange;
	}

}
