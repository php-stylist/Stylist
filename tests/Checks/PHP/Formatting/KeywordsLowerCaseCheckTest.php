<?php declare(strict_types = 1);

namespace Stylist\Tests\Checks\PHP\Formatting;

use Stylist\Checks\PHP\Formatting\KeywordsLowerCaseCheck;
use Stylist\Tests\Checks\CheckTestCase;


require_once __DIR__ . '/../../../bootstrap.php';


/**
 * @testCase
 */
final class KeywordsLowerCaseCheckTest extends CheckTestCase
{

	public function testCheck(): void
	{
		$this->assertFile(
			new KeywordsLowerCaseCheck(),
			__DIR__ . '/KeywordsLowerCaseCheckTest/source.phps',
			[
				[3, 'PHP keywords must be lower-cased, If found.'],
				[4, 'PHP keywords must be lower-cased, ECHO found.'],
				[6, 'PHP keywords must be lower-cased, ElseIf found.'],
				[9, 'PHP keywords must be lower-cased, ELSE found.'],
				[10, 'PHP keywords must be lower-cased, Echo found.'],
			]
		);
	}


	public function testFix(): void
	{
		$this->assertFixed(
			new KeywordsLowerCaseCheck(),
			__DIR__ . '/KeywordsLowerCaseCheckTest/source.phps',
			__DIR__ . '/KeywordsLowerCaseCheckTest/fix.expected.phps'
		);
	}

}


(new KeywordsLowerCaseCheckTest())->run();
