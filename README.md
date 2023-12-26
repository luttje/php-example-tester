# PHP Example Tester

Use PHP Attributes (PHP 8+) on code in your tests to automatically compile
them into your README.

<div align="center">

[![run-tests](https://github.com/luttje/php-example-tester/actions/workflows/run-tests.yml/badge.svg)](https://github.com/luttje/php-example-tester/actions/workflows/run-tests.yml)
[![Coverage Status](https://coveralls.io/repos/github/luttje/php-example-tester/badge.svg?branch=main)](https://coveralls.io/github/luttje/php-example-tester?branch=main)

</div>

> [!Warning]
> This package is still in development. It is not yet ready for production use and the API may change at any time.

## Installation

You can install the package via composer:

```bash
composer require luttje/php-example-tester
```

## Usage

TODO

## Testing

Run the tests with:

```bash
composer test
```

To run the compiler command, you can use:

```bash
php bin/console example-tester:compile --output tmp/README-tmp.md --input tests/Fixtures/ExampleClass.README.md "\Luttje\ExampleTester\Tests\Fixtures\ExampleClass"
```

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
