<?php declare(strict_types = 1);

namespace Stylist\Tests\Checks\Arrays;

use Stylist\Checks\Arrays\Syntax\ArrayDestructuringSyntaxCheck;
use Stylist\Tests\Checks\CheckTestCase;


require_once __DIR__ . '/../../../bootstrap.php';


/**
 * @testCase
 */
final class ArrayDestructuringSyntaxCheckTest extends CheckTestCase
{

	public function testCheckShort(): void
	{
		$this->assertFile(
			ArrayDestructuringSyntaxCheck::short(),
			__DIR__ . '/ArrayDestructuringSyntaxCheckTest/source.phps',
			[
				[12, 'Short array destructuring syntax must be used, list() found.'],
			]
		);
	}


	public function testFixShort(): void
	{
		$this->assertFixed(
			ArrayDestructuringSyntaxCheck::short(),
			__DIR__ . '/ArrayDestructuringSyntaxCheckTest/fix.long.phps',
			__DIR__ . '/ArrayDestructuringSyntaxCheckTest/fix.short.phps'
		);
	}


	public function testCheckLong(): void
	{
		$this->assertFile(
			ArrayDestructuringSyntaxCheck::long(),
			__DIR__ . '/ArrayDestructuringSyntaxCheckTest/source.phps',
			[
				[6, 'list() must be used for array destructuring, short syntax found.'],
				[9, 'list() must be used for array destructuring, short syntax found.'],
			]
		);
	}


	public function testFixLong(): void
	{
		$this->assertFixed(
			ArrayDestructuringSyntaxCheck::long(),
			__DIR__ . '/ArrayDestructuringSyntaxCheckTest/fix.short.phps',
			__DIR__ . '/ArrayDestructuringSyntaxCheckTest/fix.long.phps'
		);
	}

}


(new ArrayDestructuringSyntaxCheckTest())->run();
