<?php declare(strict_types = 1);

namespace Stylist\Tests\Checks\PHP\Formatting;

use Stylist\Checks\PHP\Formatting\TrueFalseNullCaseCheck;
use Stylist\Tests\Checks\CheckTestCase;


require_once __DIR__ . '/../../../bootstrap.php';


/**
 * @testCase
 */
final class TrueFalseNullCaseCheckTest extends CheckTestCase
{

	public function testCheckLower(): void
	{
		$this->assertFile(
			TrueFalseNullCaseCheck::lower(),
			__DIR__ . '/TrueFalseNullCaseCheckTest/source.phps',
			[
				[3, 'PHP constants (true, false, null) must be lower-cased, True found.'],
				[5, 'PHP constants (true, false, null) must be lower-cased, NULL found.'],
			]
		);
	}


	public function testFixLower(): void
	{
		$this->assertFixed(
			TrueFalseNullCaseCheck::lower(),
			__DIR__ . '/TrueFalseNullCaseCheckTest/source.phps',
			__DIR__ . '/TrueFalseNullCaseCheckTest/fix.lower.phps'
		);
	}


	public function testCheckUpper(): void
	{
		$this->assertFile(
			TrueFalseNullCaseCheck::upper(),
			__DIR__ . '/TrueFalseNullCaseCheckTest/source.phps',
			[
				[3, 'PHP constants (TRUE, FALSE, NULL) must be upper-cased, True found.'],
				[4, 'PHP constants (TRUE, FALSE, NULL) must be upper-cased, false found.'],
			]
		);
	}


	public function testFixUpper(): void
	{
		$this->assertFixed(
			TrueFalseNullCaseCheck::upper(),
			__DIR__ . '/TrueFalseNullCaseCheckTest/source.phps',
			__DIR__ . '/TrueFalseNullCaseCheckTest/fix.upper.phps'
		);
	}

}


(new TrueFalseNullCaseCheckTest())->run();
