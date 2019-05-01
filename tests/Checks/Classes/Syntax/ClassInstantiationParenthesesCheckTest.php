<?php declare(strict_types = 1);

namespace Stylist\Tests\Checks\Classes\Syntax;

use Stylist\Checks\Classes\Syntax\ClassInstantiationParenthesesCheck;
use Stylist\Tests\Checks\CheckTestCase;


require_once __DIR__ . '/../../../bootstrap.php';


/**
 * @testCase
 */
final class ClassInstantiationParenthesesCheckTest extends CheckTestCase
{

	public function testCheckEnforce(): void
	{
		$this->assertFile(
			ClassInstantiationParenthesesCheck::enforce(),
			__DIR__ . '/ClassInstantiationParenthesesCheckTest/source.phps',
			[
				[5, 'Class must always be instantiated with parentheses.'],
			]
		);
	}


	public function testCheckDisallow(): void
	{
		$this->assertFile(
			ClassInstantiationParenthesesCheck::disallow(),
			__DIR__ . '/ClassInstantiationParenthesesCheckTest/source.phps',
			[
				[6, 'Class must be instantiated without parentheses if possible.'],
			]
		);
	}


	public function testFixEnforce(): void
	{
		$this->assertFixed(
			ClassInstantiationParenthesesCheck::enforce(),
			__DIR__ . '/ClassInstantiationParenthesesCheckTest/source.phps',
			__DIR__ . '/ClassInstantiationParenthesesCheckTest/fix.enforce.phps'
		);
	}


	public function testFixDisallow(): void
	{
		$this->assertFixed(
			ClassInstantiationParenthesesCheck::disallow(),
			__DIR__ . '/ClassInstantiationParenthesesCheckTest/source.phps',
			__DIR__ . '/ClassInstantiationParenthesesCheckTest/fix.disallow.phps'
		);
	}

}


(new ClassInstantiationParenthesesCheckTest())->run();
