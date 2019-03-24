<?php declare(strict_types = 1);

namespace Stylist\Tests\DI;

use Nette\Configurator;
use Nette\DI\Compiler;
use Stylist\Checks\CheckInterface;
use Stylist\DI\ChecksExtension;
use Stylist\Tests\DummyCheck;
use const Stylist\Tests\TEMP_DIR;
use Tester\Assert;
use Tester\FileMock;
use Tester\TestCase;


require_once __DIR__ . '/../bootstrap.php';


/**
 * @testCase
 */
final class ChecksExtensionTest extends TestCase
{

	public function testExtension(): void
	{
		$configurator = new Configurator();
		$configurator->defaultExtensions = [];
		$configurator->setTempDirectory(TEMP_DIR);
		$configurator->setDebugMode(false);

		$configurator->onCompile[] = function (Configurator $configurator, Compiler $compiler): void {
			$compiler->addExtension('checks', new ChecksExtension());
		};

		$configurator->addConfig(FileMock::create(<<<CONFIG
checks:
	- Stylist\Tests\DummyCheck
	- Stylist\Tests\DummyCheck(dummyArgument)
CONFIG
		, 'neon'));

		$container = $configurator->createContainer();
		$checks = $container->findByType(CheckInterface::class);
		Assert::count(2, $checks);

		$checkInstance1 = $container->getService($checks[0]);
		Assert::type(DummyCheck::class, $checkInstance1);
		Assert::null($checkInstance1->constructorParameter);

		$checkInstance2 = $container->getService($checks[1]);
		Assert::type(DummyCheck::class, $checkInstance2);
		Assert::same('dummyArgument', $checkInstance2->constructorParameter);
	}

}


(new ChecksExtensionTest())->run();
