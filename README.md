# PHP Example Tester

Use comments in markdown to automatically compile tested examples into your README.

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

### 1. Prepare your README

Add the start and end comments to your README where you want each individual example to appear:

```html
### `exampleMethod`

Document the example method here, as you normally would.

**Here's the example:**

<!-- #EXAMPLE_COPY_START = { "method": "\\Luttje\\ExampleTester\\Tests\\Fixtures\\ExampleClassTest::exampleMethod" } -->

This part will be replaced with the example code in a markdown code block for php.

<!-- #EXAMPLE_COPY_END -->
```

*For a full example have a look at [👀 the example README with placeholders](tests/Fixtures/ExampleClassTest.README.md).*

### 2. Write tests for that specific example code

Write your tests in a separate static method in your test class. This package
can then extract the body of the method and use it as the example code.

<!-- #EXAMPLE_COPY_START = { "class": "\\Luttje\\ExampleTester\\Tests\\Fixtures\\ExampleClassTest" } -->

TODO

<!-- #EXAMPLE_COPY_END -->

### 3. Compile the examples into your README

Run the `example-tester` command to compile the examples into your README using
the `vendor/bin/example-tester compile` command.

```bash
vendor/bin/example-tester compile
```

After running the command, the examples will be compiled into your README. This is a section in your README may look like after running the command:

> ### `exampleMethod`
>
> This is an example description.
> It can be multiple lines long and **formatted**.
>
> <!-- #EXAMPLE_COPY_START = { "method": "\\Luttje\\ExampleTester\\Tests\\Fixtures\\ExampleClassTest::exampleMethod" } -->
>
> ```php
> // This is an example method.
> $a = 1;
> $b = 25;
> 
> $c = $a + $b;
> 
> echo $c;
> // This is the end of the example method.
> ```
>
> <!-- #EXAMPLE_COPY_END -->
>
> *🤓 Yay calculations!*

*For a full example have a look at [🏗 the compiled example README](tests/Fixtures/ExampleClassTest.README.expected.md).*

> [!Note]
> You can make your workflow even smoother by adding the `example-tester compile`
> command to your `composer.json` scripts. That way you can run it with
> `composer compile-readme`.

```json
{
    "scripts": {
        "compile-readme": [
            "vendor/bin/example-tester compile"
        ]
    }
}
```

## Options

The command has the following signature:

```bash
compile [--output OUTPUT] [--input INPUT]
```

**You must run the command from the root of your project.** That way the command
can find the vendor directory containing the composer autoload file.

### `--output`

The path to the README file to write the compiled examples to. Defaults to `README.md` in the root of your project.

```bash
vendor/bin/example-tester compile --output DOCS.md \\Your\\Autoloaded\\Namespace\\
```

### `--input`

The path to the README file to read the examples from. Defaults to the `--output` path.

## Testing

Run the tests with:

```bash
composer test
```

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
