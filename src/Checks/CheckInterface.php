<?php declare(strict_types = 1);

namespace Stylist\Checks;

use Stylist\File;


interface CheckInterface
{

	public function check(File $file): void;

}
