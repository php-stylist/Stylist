<?php declare(strict_types = 1);

namespace Stylist\Tests\Checks\Arrays;

use Stylist\Checks\Arrays\ShortArraySyntaxCheck;
use Stylist\Tests\Checks\CheckTestCase;


require_once __DIR__ . '/../../bootstrap.php';


/**
 * @testCase
 */
final class ShortArraySyntaxCheckTest extends CheckTestCase
{

	public function testCheck(): void
	{
		$this->assertFile(
			new ShortArraySyntaxCheck(),
			__DIR__ . '/ShortArraySyntaxCheckTest/source.phps',
			[
				[16, 'Short array syntax must be used, array() found.'],
			]
		);
	}

}


(new ShortArraySyntaxCheckTest())->run();
