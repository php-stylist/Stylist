<?php declare(strict_types = 1);

namespace Stylist\Tests\Checks\Classes\NamingConventions;

use Stylist\Checks\Classes\NamingConventions\ClassTypeNamingConventionCheck;
use Stylist\Tests\Checks\CheckTestCase;


require_once __DIR__ . '/../../../bootstrap.php';


/**
 * @testCase
 */
final class ClassTypeNamingConventionCheckTest extends CheckTestCase
{

	public function testCheck(): void
	{
		$this->assertFile(
			new ClassTypeNamingConventionCheck(),
			__DIR__ . '/ClassTypeNamingConventionCheckTest/source.phps',
			[
				[3, 'All class type names must be PascalCased, foo found.'],
				[7, 'All class type names must be PascalCased, bar_baz found.'],
				[9, 'All class type names must be PascalCased, FOO_BAR found.'],
			]
		);
	}

}


(new ClassTypeNamingConventionCheckTest())->run();
