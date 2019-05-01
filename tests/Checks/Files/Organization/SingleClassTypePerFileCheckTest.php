<?php declare(strict_types = 1);

namespace Stylist\Tests\Checks\Files\Organization;

use Stylist\Checks\Files\Organization\SingleClassTypePerFileCheck;
use Stylist\Tests\Checks\CheckTestCase;


require_once __DIR__ . '/../../../bootstrap.php';


/**
 * @testCase
 */
final class SingleClassTypePerFileCheckTest extends CheckTestCase
{

	public function testCheck(): void
	{
		$this->assertFile(
			new SingleClassTypePerFileCheck(),
			__DIR__ . '/SingleClassTypePerFileCheckTest/source.phps',
			[
				[12, 'There must be only one type (class, interface or trait) per file, 3 found.'],
			]
		);
	}

}


(new SingleClassTypePerFileCheckTest())->run();
