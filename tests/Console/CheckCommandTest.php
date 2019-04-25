<?php declare(strict_types = 1);

namespace Stylist\Tests\Console;

use Nette\DI\Container;
use Stylist\CheckResult;
use Stylist\Console\CheckCommand;
use Stylist\DI\ContainerFactory;
use Stylist\Output\OutputChain;
use Stylist\Stylist;
use Symfony\Component\Console\Tester\CommandTester;
use Tester\Assert;
use Tester\Environment;
use Tester\TestCase;
use Webmozart\PathUtil\Path;


require_once __DIR__ . '/../bootstrap.php';


/**
 * @testCase
 */
final class CheckCommandTest extends TestCase
{

	protected function tearDown(): void
	{
		\Mockery::close();
	}


	public function testSuccess(): void
	{
		$input = [
			'--config' => __DIR__ . '/../stylist.neon',
			'paths' => [__DIR__ . '/../dummy'],
		];

		$stylistMock = $this->mockStylist(
			['*.php', '*.phpt'],
			[''],
			false,
			[Path::canonicalize(__DIR__ . '/../dummy')],
			true
		);

		$command = $this->createCommand(
			__DIR__,
			Path::canonicalize(__DIR__ . '/../stylist.neon'),
			null,
			$stylistMock
		);

		$this->runCommand($command, $input, 0);
	}


	public function testFailure(): void
	{
		$input = [
			'--config' => $configFile = __DIR__ . '/../stylist.neon',
			'paths' => $paths = [__DIR__ . '/../dummy'],
		];

		$stylistMock = $this->mockStylist(
			['*.php', '*.phpt'],
			[''],
			false,
			[Path::canonicalize(__DIR__ . '/../dummy')],
			false
		);

		$command = $this->createCommand(
			__DIR__,
			Path::canonicalize(__DIR__ . '/../stylist.neon'),
			null,
			$stylistMock
		);

		$this->runCommand($command, $input, 1);
	}


	public function testRelativePaths(): void
	{
		$input = [
			'--config' => '../stylist.neon',
			'paths' => ['../dummy'],
		];

		$stylistMock = $this->mockStylist(
			['*.php', '*.phpt'],
			[''],
			false,
			[Path::canonicalize(__DIR__ . '/../dummy')],
			true
		);

		$command = $this->createCommand(
			__DIR__,
			Path::canonicalize(__DIR__ . '/../stylist.neon'),
			null,
			$stylistMock
		);

		$this->runCommand($command, $input, 0);
	}


	public function testDefaultConfigFile(): void
	{
		$input = [
			'paths' => [__DIR__ . '/../dummy'],
		];

		$stylistMock = $this->mockStylist(
			['*.php', '*.phpt'],
			[''],
			false,
			[Path::canonicalize(__DIR__ . '/../dummy')],
			true
		);

		$command = $this->createCommand(
			__DIR__ . '/..',
			Path::canonicalize(__DIR__ . '/../stylist.neon'),
			null,
			$stylistMock
		);

		$this->runCommand($command, $input, 0);
	}


	public function testAcceptedExcluded(): void
	{
		$input = [
			'--config' => __DIR__ . '/../stylist.neon',
			'--accept' => '*.phps,*.inc',
			'--exclude' => '*.php',
			'paths' => [__DIR__ . '/../dummy'],
		];

		$stylistMock = $this->mockStylist(
			['*.phps', '*.inc'],
			['*.php'],
			false,
			[Path::canonicalize(__DIR__ . '/../dummy')],
			true
		);

		$command = $this->createCommand(
			__DIR__,
			Path::canonicalize(__DIR__ . '/../stylist.neon'),
			null,
			$stylistMock
		);

		$this->runCommand($command, $input, 0);
	}


	public function testDryRun(): void
	{
		$input = [
			'--config' => __DIR__ . '/../stylist.neon',
			'--dry-run' => true,
			'paths' => [__DIR__ . '/../dummy'],
		];

		$stylistMock = $this->mockStylist(
			['*.php', '*.phpt'],
			[''],
			true,
			[Path::canonicalize(__DIR__ . '/../dummy')],
			true
		);

		$command = $this->createCommand(
			__DIR__,
			Path::canonicalize(__DIR__ . '/../stylist.neon'),
			null,
			$stylistMock
		);

		$this->runCommand($command, $input, 0);
	}


	public function testTempDirectory(): void
	{
		$input = [
			'--config' => __DIR__ . '/../stylist.neon',
			'--temp' => __DIR__ . '/../temp',
			'paths' => [__DIR__ . '/../dummy'],
		];

		$stylistMock = $this->mockStylist(
			['*.php', '*.phpt'],
			[''],
			false,
			[Path::canonicalize(__DIR__ . '/../dummy')],
			true
		);

		$command = $this->createCommand(
			__DIR__,
			Path::canonicalize(__DIR__ . '/../stylist.neon'),
			Path::canonicalize(__DIR__ . '/../temp'),
			$stylistMock
		);

		$this->runCommand($command, $input, 0);
	}


	private function mockStylist(
		array $expectedAccepted,
		array $expectedExcluded,
		bool $expectedDryRun,
		array $expectedPaths,
		bool $success
	): Stylist
	{
		$stylistMock = \Mockery::mock(Stylist::class);
		$stylistMock->shouldReceive('accept')->with($expectedAccepted)->once()->andReturnSelf();
		$stylistMock->shouldReceive('exclude')->with($expectedExcluded)->once()->andReturnSelf();
		$stylistMock->shouldReceive('dryRun')->with($expectedDryRun)->once()->andReturnSelf();
		$stylistMock->shouldReceive('check')
			->once()
			->with($expectedPaths)
			->andReturn(new CheckResult($success, [], 0.0));

		return $stylistMock;
	}


	private function createCommand(
		string $workingDirectory,
		string $expectedConfigFile,
		?string $expectedTempDirectory,
		Stylist $stylistMock
	): CheckCommand
	{
		$container = \Mockery::mock(Container::class);
		$container->shouldReceive('getByType')
			->with(Stylist::class)
			->once()
			->andReturn($stylistMock);

		$containerFactory = \Mockery::mock(ContainerFactory::class);
		$containerFactory->shouldReceive('create')
			->with(
				\Mockery::type(OutputChain::class),
				$expectedConfigFile,
				$expectedTempDirectory
			)
			->once()
			->andReturn($container);

		return new CheckCommand($workingDirectory, $containerFactory);
	}


	private function runCommand(
		CheckCommand $command,
		array $inputArray,
		int $expectedExitCode
	): void
	{
		$commandTester = new CommandTester($command);
		$exitCode = $commandTester->execute($inputArray);
		Assert::same($expectedExitCode, $exitCode);
	}

}


Environment::bypassFinals();
(new CheckCommandTest())->run();
