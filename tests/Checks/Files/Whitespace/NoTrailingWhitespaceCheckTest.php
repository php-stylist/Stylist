<?php declare(strict_types = 1);

namespace Stylist\Tests\Checks\Files\Whitespace;

use Stylist\Checks\Files\Whitespace\NoTrailingWhitespaceCheck;
use Stylist\Tests\Checks\CheckTestCase;


require_once __DIR__ . '/../../../bootstrap.php';


/**
 * @testCase
 */
final class NoTrailingWhitespaceCheckTest extends CheckTestCase
{

	public function testCheck(): void
	{
		$this->assertFile(
			new NoTrailingWhitespaceCheck(),
			__DIR__ . '/NoTrailingWhitespaceCheckTest/source.phps',
			[
				[3, 'Trailing whitespace found. There should be none.'],
				[4, 'Trailing whitespace found. There should be none.'],
			]
		);
	}


	public function testFix(): void
	{
		$this->assertFixed(
			new NoTrailingWhitespaceCheck(),
			__DIR__ . '/NoTrailingWhitespaceCheckTest/source.phps',
			__DIR__ . '/NoTrailingWhitespaceCheckTest/fix.expected.phps'
		);
	}

}


(new NoTrailingWhitespaceCheckTest())->run();
