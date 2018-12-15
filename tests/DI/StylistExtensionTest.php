<?php declare(strict_types = 1);

namespace Stylist\Tests\DI;

use Nette\Configurator;
use Nette\DI\Compiler;
use Stylist\CheckResult;
use Stylist\DI\IgnoredIssuesExtension;
use Stylist\DI\StylistExtension;
use Stylist\File;
use Stylist\Output\OutputInterface;
use Stylist\Stylist;
use const Stylist\Tests\TEMP_DIR;
use Tester\Assert;
use Tester\TestCase;


require_once __DIR__ . '/../bootstrap.php';


/**
 * @testCase
 */
final class StylistExtensionTest extends TestCase
{

	public function testExtension(): void
	{
		$configurator = new Configurator();
		$configurator->defaultExtensions = [];
		$configurator->setTempDirectory(TEMP_DIR);
		$configurator->setDebugMode(false);

		$configurator->onCompile[] = function (Configurator $configurator, Compiler $compiler): void {
			$compiler->addExtension('stylist', new StylistExtension());
			$compiler->addExtension(null, new IgnoredIssuesExtension());
		};

		$configurator->addServices([
			'stylist.output' => new class implements OutputInterface {
				public function initialize(array $paths): void {}
				public function checkedFile(File $file): void {}
				public function finish(CheckResult $result): void {}
			},
		]);

		$container = $configurator->createContainer();
		Assert::type(Stylist::class, $container->getByType(Stylist::class));
	}

}


(new StylistExtensionTest())->run();
