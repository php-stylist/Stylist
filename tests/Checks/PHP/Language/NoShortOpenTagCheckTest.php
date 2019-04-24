<?php declare(strict_types = 1);

namespace Stylist\Tests\Checks\PHP\Language;

use Stylist\Checks\PHP\Language\NoShortOpenTagCheck;
use Stylist\Tests\Checks\CheckTestCase;


require_once __DIR__ . '/../../../bootstrap.php';


/**
 * @testCase
 */
final class NoShortOpenTagCheckTest extends CheckTestCase
{

	public function testCheck(): void
	{
		$this->assertFile(
			new NoShortOpenTagCheck(),
			__DIR__ . '/NoShortOpenTagCheckTest/source.phps',
			[
				[1, 'File must not use short PHP open tag, <? found.'],
			]
		);
	}


	public function testFix(): void
	{
		$this->assertFixed(
			new NoShortOpenTagCheck(),
			__DIR__ . '/NoShortOpenTagCheckTest/source.phps',
			__DIR__ . '/NoShortOpenTagCheckTest/fix.expected.phps'
		);
	}

}


(new NoShortOpenTagCheckTest())->run();
