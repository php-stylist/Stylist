<?php declare(strict_types = 1);

namespace Stylist\Tests\Checks\Files\Whitespace;

use Stylist\Checks\Files\Whitespace\IndentationCheck;
use Stylist\Tests\Checks\CheckTestCase;


require_once __DIR__ . '/../../../bootstrap.php';


/**
 * @testCase
 */
final class IndentationCheckTest extends CheckTestCase
{

	public function testCheckTabs(): void
	{
		$this->assertFile(
			IndentationCheck::tabs(),
			__DIR__ . '/IndentationCheckTest/source.phps',
			[
				[9, 'Wrong indentation found. Tabs must be used.'],
				[11, 'Wrong indentation found. Tabs must be used.'],
			]
		);
	}


	public function testFixTabs(): void
	{
		$this->assertFixed(
			IndentationCheck::tabs(),
			__DIR__ . '/IndentationCheckTest/source.phps',
			__DIR__ . '/IndentationCheckTest/fix.tabs.phps'
		);
	}


	public function testCheckSpaces(): void
	{
		$this->assertFile(
			IndentationCheck::spaces(),
			__DIR__ . '/IndentationCheckTest/source.phps',
			[
				[4, 'Wrong indentation found. Exactly 4 spaces must be used.'],
				[11, 'Wrong indentation found. Exactly 4 spaces must be used.'],
				[13, 'Wrong indentation found. Exactly 4 spaces must be used.'],
			]
		);
	}


	public function testFixSpaces(): void
	{
		$this->assertFixed(
			IndentationCheck::spaces(),
			__DIR__ . '/IndentationCheckTest/source.phps',
			__DIR__ . '/IndentationCheckTest/fix.spaces.phps'
		);
	}

}


(new IndentationCheckTest())->run();
