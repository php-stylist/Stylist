<?php declare(strict_types = 1);

namespace Stylist\Tests\Checks\Files\Language;

use Stylist\Checks\Files\Language\NoCloseTagCheck;
use Stylist\Tests\Checks\CheckTestCase;


require_once __DIR__ . '/../../../bootstrap.php';


/**
 * @testCase
 */
final class NoCloseTagCheckTest extends CheckTestCase
{

	public function testCheck(): void
	{
		$this->assertFile(
			new NoCloseTagCheck(),
			__DIR__ . '/NoCloseTagCheckTest/source.phps',
			[
				[5, 'Close tag found. Files containing only PHP code must not contain the PHP close tag.'],
			]
		);
	}


	public function testFix(): void
	{
		$this->assertFixed(
			new NoCloseTagCheck(),
			__DIR__ . '/NoCloseTagCheckTest/source.phps',
			__DIR__ . '/NoCloseTagCheckTest/fix.expected.phps'
		);
	}

}


(new NoCloseTagCheckTest())->run();
