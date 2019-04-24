<?php declare(strict_types = 1);

namespace Stylist\Tests\DI;

use Nette\Configurator;
use Nette\DI\Compiler;
use Stylist\DI\IgnoredIssuesExtension;
use Stylist\IgnoredIssues\IgnoredIssues;
use Stylist\Tests\DummyCheck;
use const Stylist\Tests\TEMP_DIR;
use Tester\Assert;
use Tester\FileMock;
use Tester\TestCase;


require_once __DIR__ . '/../bootstrap.php';


/**
 * @testCase
 */
final class IgnoredIssuesExtensionTest extends TestCase
{

	public function testExtension(): void
	{
		$configurator = new Configurator();
		$configurator->defaultExtensions = [];
		$configurator->setTempDirectory(TEMP_DIR);
		$configurator->setDebugMode(false);

		$configurator->onCompile[] = function (Configurator $configurator, Compiler $compiler): void {
			$compiler->addExtension('ignoredIssues', new IgnoredIssuesExtension());
		};

		$configurator->addDynamicParameters(['workingDirectory' => __DIR__]);
		$configurator->addConfig(FileMock::create(<<<CONFIG
ignoredIssues:
	-
		check: Stylist\Tests\DummyCheck
		file: %workingDirectory%/IgnoredIssuesExtensionTest.phpt
		line: 42
CONFIG
		, 'neon'));

		$container = $configurator->createContainer();
		$ignoredIssues = $container->getByType(IgnoredIssues::class);
		Assert::type(IgnoredIssues::class, $ignoredIssues);

		$ignoredIssuesArray = $ignoredIssues->listUnmatched();
		Assert::count(1, $ignoredIssuesArray);
		Assert::same(DummyCheck::class, $ignoredIssuesArray[0]->getCheckName());
		Assert::same(42, $ignoredIssuesArray[0]->getLine());
	}

}


(new IgnoredIssuesExtensionTest())->run();
