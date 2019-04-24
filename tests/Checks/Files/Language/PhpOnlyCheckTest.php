<?php declare(strict_types = 1);

namespace Stylist\Tests\Checks\Files\Language;

use Stylist\Checks\Files\Language\PhpOnlyCheck;
use Stylist\Tests\Checks\CheckTestCase;


require_once __DIR__ . '/../../../bootstrap.php';


/**
 * @testCase
 */
final class PhpOnlyCheckTest extends CheckTestCase
{

	public function testCheck(): void
	{
		$this->assertFile(
			new PhpOnlyCheck(),
			__DIR__ . '/PhpOnlyCheckTest/source.phps',
			[
				[6, 'File must contain only PHP code, inline HTML found.'],
			]
		);
	}

}


(new PhpOnlyCheckTest())->run();
