<?php declare(strict_types = 1);

namespace Stylist\Tests\Checks\Files\Formatting;

use Stylist\Checks\Files\Formatting\LineLengthCheck;
use Stylist\Tests\Checks\CheckTestCase;


require_once __DIR__ . '/../../../bootstrap.php';


/**
 * @testCase
 */
final class LineLengthCheckTest extends CheckTestCase
{

	public function testCheck(): void
	{
		$this->assertFile(
			new LineLengthCheck(80),
			__DIR__ . '/LineLengthCheckTest/source.phps',
			[
				[8, 'Line must not be longer than 80 characters, 82 found.'],
			]
		);

		// check with a different tab width
		$this->assertFile(
			new LineLengthCheck(80, 8),
			__DIR__ . '/LineLengthCheckTest/source.phps',
			[
				[6, 'Line must not be longer than 80 characters, 83 found.'],
				[8, 'Line must not be longer than 80 characters, 82 found.'],
			]
		);
	}

}


(new LineLengthCheckTest())->run();
