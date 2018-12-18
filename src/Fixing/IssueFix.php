<?php declare(strict_types = 1);

namespace Stylist\Fixing;


final class IssueFix
{

	/** @var callable */
	private $fixer;

	/** @var bool */
	private $fixed = false;


	public function __construct(callable $fixer)
	{
		$this->fixer = $fixer;
	}


	public function apply(ChangeSet $changeSet): void
	{
		$localChangeSet = new ChangeSet();
		($this->fixer)($localChangeSet);

		try {
			$changeSet->merge($localChangeSet);
			$this->fixed = true;

		} catch (ConflictingChangesException $e) {
			$this->fixed = false;
		}
	}


	public function isFixed(): bool
	{
		return $this->fixed;
	}

}
