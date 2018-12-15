<?php declare(strict_types = 1);

namespace Stylist\Tests\DI;

use Stylist\CheckResult;
use Stylist\DI\ContainerFactory;
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
final class ContainerFactoryTest extends TestCase
{

	public function testContainerFactory(): void
	{
		$containerFactory = new ContainerFactory(__DIR__);
		$container = $containerFactory->create(
			new class implements OutputInterface {
				public function initialize(array $paths): void {}
				public function checkedFile(File $file): void {}
				public function finish(CheckResult $result): void {}
			},
			__DIR__ . '/../stylist.neon',
			TEMP_DIR
		);

		Assert::type(Stylist::class, $container->getByType(Stylist::class));
	}

}


(new ContainerFactoryTest())->run();
