<?php declare(strict_types = 1);

namespace Stylist\DI;

use Nette\DI\CompilerExtension;
use Stylist\Checks\CheckInterface;


final class ChecksExtension extends CompilerExtension
{

	public function loadConfiguration(): void
	{
		$builder = $this->getContainerBuilder();

		$counter = 1;
		foreach ((array) $this->getConfig() as $check) {
			$builder->addDefinition($this->prefix((string) $counter++))
				->setType(CheckInterface::class)
				->setFactory($check);
		}
	}

}
