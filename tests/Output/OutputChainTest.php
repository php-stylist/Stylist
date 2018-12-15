<?php declare(strict_types = 1);

namespace Stylist\Tests\Output;

use Mockery\MockInterface;
use Stylist\CheckResult;
use Stylist\File;
use Stylist\Output\OutputChain;
use Stylist\Output\OutputInterface;
use Stylist\Tokenista\Tokens;
use Tester\Environment;
use Tester\TestCase;


require_once __DIR__ . '/../bootstrap.php';


/**
 * @testCase
 */
final class OutputChainTest extends TestCase
{

	public function testOutput(): void
	{
		$output1 = $this->mockOutput();
		$output2 = $this->mockOutput();

		$output = new OutputChain();
		$output->addOutput($output1);
		$output->addOutput($output2);

		$output->initialize(['foo']);
		$output->checkedFile(new File(new \SplFileInfo(__FILE__), Tokens::from('<?php'), []));
		$output->finish(new CheckResult(true, [], 0.42));

		Environment::$checkAssertions = false;
		\Mockery::close();
	}


	private function mockOutput(): MockInterface
	{
		$output = \Mockery::mock(OutputInterface::class);
		$output->shouldReceive('initialize')->with(['foo'])->once();
		$output->shouldReceive('checkedFile')->with(\Mockery::type(File::class))->once();
		$output->shouldReceive('finish')->with(\Mockery::type(CheckResult::class))->once();

		return $output;
	}

}


(new OutputChainTest())->run();
