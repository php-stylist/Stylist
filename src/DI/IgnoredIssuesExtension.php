<?php declare(strict_types = 1);

namespace Stylist\DI;

use Nette\DI\CompilerExtension;
use Nette\Schema\Expect;
use Nette\Schema\Schema;
use Stylist\IgnoredIssues\IgnoredIssue;
use Stylist\IgnoredIssues\IgnoredIssues;


final class IgnoredIssuesExtension extends CompilerExtension
{

	public function loadConfiguration(): void
	{
		$ignoredIssues = [];
		foreach ((array) $this->getConfig() as $ignoredIssue) {
			$ignoredIssues[] = new IgnoredIssue(
				$ignoredIssue->check,
				$ignoredIssue->file,
				$ignoredIssue->line
			);
		}

		$builder = $this->getContainerBuilder();
		$builder->addDefinition($this->prefix('ignoredIssues'))
			->setType(IgnoredIssues::class)
			->setFactory(IgnoredIssues::class, [$ignoredIssues]);
	}


	public function getConfigSchema(): Schema
	{
		return Expect::listOf(
			Expect::structure([
				'check' => Expect::type('class')->required(),
				'file' => Expect::string()->required(),
				'line' => Expect::int()->required(),
			])
		);
	}

}
