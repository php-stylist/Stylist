<?php declare(strict_types = 1);

namespace Stylist\Output;

use Stylist\CheckResult;
use Stylist\File;


final class OutputChain implements OutputInterface
{

	/** @var OutputInterface[] */
	private $outputs = [];


	public function addOutput(OutputInterface $output)
	{
		$this->outputs[] = $output;
	}


	public function initialize(array $paths): void
	{
		foreach ($this->outputs as $output) {
			$output->initialize($paths);
		}
	}


	public function checkedFile(File $file): void
	{
		foreach ($this->outputs as $output) {
			$output->checkedFile($file);
		}
	}


	public function finish(CheckResult $result): void
	{
		foreach ($this->outputs as $output) {
			$output->finish($result);
		}
	}

}
