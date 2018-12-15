<?php declare(strict_types = 1);

namespace Stylist;

use Nette\Utils\Finder;
use Stylist\Checks\CheckInterface;
use Stylist\Output\OutputInterface;


final class Stylist
{

	/** @var CheckInterface[] */
	private $checks;

	/** @var OutputInterface */
	private $output;

	/** @var FileFactory */
	private $fileFactory;

	/** @var string[] */
	private $accepted = [];

	/** @var string[] */
	private $excluded = [];

	/** @var File[] */
	private $files = [];


	/**
	 * @param CheckInterface[] $checks
	 */
	public function __construct(
		array $checks,
		OutputInterface $output,
		FileFactory $fileFactory
	)
	{
		$this->checks = $checks;
		$this->output = $output;
		$this->fileFactory = $fileFactory;
	}


	public function accept(array $accepted): self
	{
		$this->accepted = $accepted;
		return $this;
	}


	public function exclude(array $excluded): self
	{
		$this->excluded = $excluded;
		return $this;
	}


	/**
	 * @param string[] $paths
	 */
	public function check(array $paths): CheckResult
	{
		\set_time_limit(0);

		$this->output->initialize($paths);

		$start = \microtime(true);
		$success = true;
		$checkedFileNames = [];

		foreach ($this->getFiles($paths) as $file) {
			if (\in_array($file->getPathname(), $checkedFileNames, true)) {
				continue;
			}

			$checkedFileNames[] = $file->getPathname();
			$this->files[] = $checkedFile = $this->fileFactory->create($file);
			$success = $this->checkFile($checkedFile) && $success;
			$this->output->checkedFile($checkedFile);
		}

		$timeTaken = \microtime(true) - $start;
		$result = new CheckResult(
			$success,
			$this->files,
			$timeTaken
		);

		$this->output->finish($result);
		return $result;
	}


	/**
	 * @param string[] $paths
	 * @return \SplFileInfo[]|\Generator
	 */
	private function getFiles(array $paths): \Generator
	{
		foreach ($paths as $path) {
			if (\is_file($path)) {
				yield $path;

			} else {
				yield from Finder::findFiles($this->accepted)
					->exclude($this->excluded)
					->from($path)
					->exclude($this->excluded);
			}
		}
	}


	private function checkFile(File $file): bool
	{
		\set_error_handler(static function ($severity, $message, $file, $line): void {
			throw new \ErrorException($message, 0, $severity, $file, $line);
		});

		try {
			foreach ($this->checks as $check) {
				$check->check($file);
			}

			$file->finishedCheck();

		} catch (\Throwable $error) {
			$file->setCheckError($error);
		}

		\restore_error_handler();
		return $file->isOk();
	}

}
