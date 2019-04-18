<?php declare(strict_types = 1);

namespace Stylist\Tests\Checks\Files\Formatting;

use Stylist\Checks\Files\Formatting\EndOfFileNewlineCheck;
use Stylist\Tests\Checks\CheckTestCase;


require_once __DIR__ . '/../../../bootstrap.php';


/**
 * @testCase
 */
final class EndOfFileNewlineCheckTest extends CheckTestCase
{

	public function testCheck(): void
	{
		$this->assertFile(
			new EndOfFileNewlineCheck(),
			__DIR__ . '/EndOfFileNewlineCheckTest/source.phps',
			[
				[3, 'File must end with a newline character.']
			]
		);
	}


	public function testFix(): void
	{
		$this->assertFixed(
			new EndOfFileNewlineCheck(),
			__DIR__ . '/EndOfFileNewlineCheckTest/source.phps',
			__DIR__ . '/EndOfFileNewlineCheckTest/fix.expected.phps'
		);
	}

}


(new EndOfFileNewlineCheckTest())->run();
