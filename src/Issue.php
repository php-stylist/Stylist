<?php declare(strict_types = 1);

namespace Stylist;

use Stylist\Checks\CheckInterface;


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


	public function __construct(
		File $file,
		CheckInterface $check,
		string $message,
		int $line
	)
	{
		$this->file = $file;
		$this->check = $check;
		$this->message = $message;
		$this->line = $line;
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

}
