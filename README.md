# Stylist

[![Build Status](https://img.shields.io/travis/php-stylist/Stylist.svg)](https://travis-ci.org/php-stylist/Stylist)
[![Code Coverage](https://img.shields.io/codecov/c/github/php-stylist/Stylist.svg)](https://codecov.io/gh/php-stylist/Stylist)
[![Downloads this Month](https://img.shields.io/packagist/dm/stylist/stylist.svg)](https://packagist.org/packages/stylist/stylist)
[![Latest stable](https://img.shields.io/packagist/v/stylist/stylist.svg)](https://packagist.org/packages/stylist/stylist)

Stylist combines the power of the AST with the simplicity of code tokens to create a PHP code style checker that's
actually pleasant to use, configure, and even extend.


## Usage

1. Install via Composer:

	```bash
	$ composer require --dev stylist/stylist
	```

2. Configure in `stylist.neon`:

	```yaml
	checks:
	    - Stylist\Checks\Arrays\ShortArraySyntaxCheck
	```

3. Run

	```bash
	$ php vendor/bin/stylist check src/
	```
