<?php declare(strict_types = 1);

namespace Stylist\DI;

use Nette\DI\CompilerExtension;
use Nette\DI\Definitions\Statement;
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
			$ignoredIssues[] = new Statement(
				IgnoredIssue::class,
				[
					'checkName' => $ignoredIssue->check,
					'file' => $ignoredIssue->file,
					'line' => $ignoredIssue->line,
				]
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
				'file' => Expect::string()->required()->dynamic(),
				'line' => Expect::int()->required(),
			])
		);
	}

}
