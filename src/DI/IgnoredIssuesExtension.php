<?php declare(strict_types = 1);

namespace Stylist\DI;

use Nette\DI\CompilerExtension;
use Stylist\IgnoredIssues\IgnoredIssue;
use Stylist\IgnoredIssues\IgnoredIssues;


final class IgnoredIssuesExtension extends CompilerExtension
{

	public function loadConfiguration(): void
	{
		$ignoredIssues = [];
		foreach ($this->getConfig() as $ignoredIssue) {
			$ignoredIssues[] = new IgnoredIssue(
				$ignoredIssue['check'],
				$ignoredIssue['file'],
				$ignoredIssue['line']
			);
		}

		$builder = $this->getContainerBuilder();
		$builder->addDefinition($this->prefix('ignoredIssues'))
			->setType(IgnoredIssues::class)
			->setFactory(IgnoredIssues::class, [$ignoredIssues]);
	}

}
