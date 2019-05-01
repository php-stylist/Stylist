<?php declare(strict_types = 1);

namespace Stylist\Tests\Utils;

use Stylist\Utils\NamingConvention;
use Tester\Assert;
use Tester\TestCase;


require_once __DIR__ . '/../bootstrap.php';


/**
 * @testCase
 */
final class NamingConventionTest extends TestCase
{

	public function testPascalCased(): void
	{
		$namingConvention = NamingConvention::pascalCased();
		Assert::same('PascalCased', $namingConvention->describe());
		Assert::true($namingConvention->matches('PascalCasedName'));
		Assert::false($namingConvention->matches('camelCasedName'));
		Assert::false($namingConvention->matches('snake_lower_cased_name'));
		Assert::false($namingConvention->matches('SNAKE_UPPER_CASED_NAME'));
		Assert::false($namingConvention->matches('Mixed_CASEDName'));
	}


	public function testCamelCased(): void
	{
		$namingConvention = NamingConvention::camelCased();
		Assert::same('camelCased', $namingConvention->describe());
		Assert::false($namingConvention->matches('PascalCasedName'));
		Assert::true($namingConvention->matches('camelCasedName'));
		Assert::false($namingConvention->matches('snake_lower_cased_name'));
		Assert::false($namingConvention->matches('SNAKE_UPPER_CASED_NAME'));
		Assert::false($namingConvention->matches('Mixed_CASEDName'));
	}


	public function testSnakeLowerCased(): void
	{
		$namingConvention = NamingConvention::snakeLowerCased();
		Assert::same('snake_lower_cased', $namingConvention->describe());
		Assert::false($namingConvention->matches('PascalCasedName'));
		Assert::false($namingConvention->matches('camelCasedName'));
		Assert::true($namingConvention->matches('snake_lower_cased_name'));
		Assert::false($namingConvention->matches('SNAKE_UPPER_CASED_NAME'));
		Assert::false($namingConvention->matches('Mixed_CASEDName'));
	}


	public function testSnakeUpperCased(): void
	{
		$namingConvention = NamingConvention::snakeUpperCased();
		Assert::same('SNAKE_UPPER_CASED', $namingConvention->describe());
		Assert::false($namingConvention->matches('PascalCasedName'));
		Assert::false($namingConvention->matches('camelCasedName'));
		Assert::false($namingConvention->matches('snake_lower_cased_name'));
		Assert::true($namingConvention->matches('SNAKE_UPPER_CASED_NAME'));
		Assert::false($namingConvention->matches('Mixed_CASEDName'));
	}


	public function testCustom(): void
	{
		$namingConvention = new NamingConvention('custom', '/^[A-Z]\d+$/');
		Assert::same('custom', $namingConvention->describe());
		Assert::false($namingConvention->matches('PascalCasedName'));
		Assert::false($namingConvention->matches('camelCasedName'));
		Assert::false($namingConvention->matches('snake_lower_cased_name'));
		Assert::false($namingConvention->matches('SNAKE_UPPER_CASED_NAME'));
		Assert::false($namingConvention->matches('Mixed_CASEDName'));

		Assert::true($namingConvention->matches('X42'));
		Assert::false($namingConvention->matches('XX42'));
		Assert::false($namingConvention->matches('42'));
	}

}


(new NamingConventionTest())->run();
