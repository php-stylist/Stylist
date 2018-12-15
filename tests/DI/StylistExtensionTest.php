<?php declare(strict_types = 1);

namespace Stylist\Tests\DI;

use Nette\Configurator;
use Nette\DI\Compiler;
use Nette\DI\Container;
use Stylist\CheckResult;
use Stylist\DI\StylistExtension;
use Stylist\File;
use Stylist\Output\OutputInterface;
use Stylist\Stylist;
use const Stylist\Tests\TEMP_DIR;
use Tester\Assert;
use Tester\FileMock;
use Tester\TestCase;


require_once __DIR__ . '/../bootstrap.php';


/**
 * @testCase
 */
final class StylistExtensionTest extends TestCase
{

	public function testExtension(): void
	{
		$container = $this->createContainer();
		Assert::type(Stylist::class, $container->getByType(Stylist::class));
	}


	private function createContainer(): Container
	{
		$configurator = new Configurator();
		$configurator->defaultExtensions = [];
		$configurator->setTempDirectory(TEMP_DIR);
		$configurator->setDebugMode(FALSE);

		$configurator->onCompile[] = function (Configurator $configurator, Compiler $compiler): void {
			$compiler->addExtension('stylist', new StylistExtension());
		};

		$configurator->addServices([
			'stylist.output' => new class implements OutputInterface {
				public function initialize(array $paths): void {}
				public function checkedFile(File $file): void {}
				public function finish(CheckResult $result): void {}
			},
		]);

		return $configurator->createContainer();
	}

}


(new StylistExtensionTest())->run();
