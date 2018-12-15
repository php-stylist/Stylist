<?php declare(strict_types = 1);

namespace Stylist;


final class CheckResult
{

	/** @var bool */
	private $success;

	/** @var array */
	private $files;

	/** @var float */
	private $timeTaken;


	/**
	 * @param File[] $files
	 */
	public function __construct(
		bool $success,
		array $files,
		float $timeTaken
	)
	{
		$this->success = $success;
		$this->files = $files;
		$this->timeTaken = $timeTaken;
	}


	public function isSuccess(): bool
	{
		return $this->success;
	}


	/**
	 * @return File[]
	 */
	public function getFiles(): array
	{
		return $this->files;
	}


	public function getTimeTaken(): float
	{
		return $this->timeTaken;
	}

}
