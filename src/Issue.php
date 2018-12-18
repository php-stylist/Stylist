<?php declare(strict_types = 1);

namespace Stylist;

use Stylist\Checks\CheckInterface;
use Stylist\Fixing\IssueFix;


final class Issue
{

	/** @var File */
	private $file;

	/** @var CheckInterface */
	private $check;

	/** @var string */
	private $message;

	/** @var int */
	private $line;

	/** @var ?IssueFix */
	private $fix;


	public function __construct(
		File $file,
		CheckInterface $check,
		string $message,
		int $line,
		?callable $fixer = null
	)
	{
		$this->file = $file;
		$this->check = $check;
		$this->message = $message;
		$this->line = $line;

		if ($fixer !== null) {
			$this->fix = new IssueFix($fixer);
		}
	}


	public function getFile(): File
	{
		return $this->file;
	}


	public function getCheck(): CheckInterface
	{
		return $this->check;
	}


	public function getMessage(): string
	{
		return $this->message;
	}


	public function getLine(): int
	{
		return $this->line;
	}


	public function canBeFixed(): bool
	{
		return $this->fix !== null;
	}


	public function getFix(): ?IssueFix
	{
		return $this->fix;
	}

}
