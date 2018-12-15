<?php declare(strict_types = 1);

namespace Stylist\DI;

use Nette\Configurator;
use Nette\DI\Container;
use Nette\DI\Extensions\ExtensionsExtension;
use Nette\DI\Extensions\PhpExtension;
use Stylist\Output\OutputInterface;


final class ContainerFactory
{

	/** @var string */
	private $workingDirectory;


	public function __construct(string $workingDirectory)
	{
		$this->workingDirectory = $workingDirectory;
	}


	public function create(
		OutputInterface $output,
		string $projectConfigFile,
		?string $tempDirectory
	): Container
	{
		$configurator = new Configurator();
		$configurator->defaultExtensions = [
			'php' => PhpExtension::class,
			'extensions' => ExtensionsExtension::class,
			'stylist' => StylistExtension::class,
			'checks' => ChecksExtension::class,
			'ignoredIssues' => IgnoredIssuesExtension::class,
		];

		$configurator->setDebugMode(true);
		$configurator->setTempDirectory($tempDirectory ?? \sys_get_temp_dir());
		$configurator->addConfig($projectConfigFile);

		$configurator->addServices([
			'stylist.output' => $output,
		]);

		$configurator->addDynamicParameters([
			'workingDirectory' => $this->workingDirectory
		]);

		return $configurator->createContainer();
	}

}
