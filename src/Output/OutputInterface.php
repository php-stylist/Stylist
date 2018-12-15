<?php declare(strict_types = 1);

namespace Stylist\Output;

use Stylist\CheckResult;
use Stylist\File;


interface OutputInterface
{

	public function initialize(array $paths): void;


	public function checkedFile(File $file): void;


	public function finish(CheckResult $result): void;

}
