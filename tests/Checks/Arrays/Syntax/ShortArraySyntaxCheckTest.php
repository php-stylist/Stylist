<?php declare(strict_types = 1);

namespace Stylist\Tests\Checks\Arrays\Syntax;

use Stylist\Checks\Arrays\Syntax\ShortArraySyntaxCheck;
use Stylist\Tests\Checks\CheckTestCase;


require_once __DIR__ . '/../../../bootstrap.php';


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


	public function testFix(): void
	{
		$this->assertFixed(
			new ShortArraySyntaxCheck(),
			__DIR__ . '/ShortArraySyntaxCheckTest/fix.source.phps',
			__DIR__ . '/ShortArraySyntaxCheckTest/fix.expected.phps'
		);
	}

}


(new ShortArraySyntaxCheckTest())->run();
