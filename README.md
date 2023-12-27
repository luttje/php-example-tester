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

<!-- #EXAMPLE_COPY_IGNORE_START -->
> ````html
> ### `exampleMethod`
>
> Document the example method here, as you normally would.
>
> **Here's the example code:**
>
> <!-- #EXAMPLE_COPY_START = \Luttje\ExampleTester\Tests\Fixtures\ExampleClassTest::exampleMethod -->
>
> This will be replaced with the example code.
>
> <!-- #EXAMPLE_COPY_END -->
>
> *🤓 Yay calculations!*
> ````
<!-- #EXAMPLE_COPY_IGNORE_END -->

*For a full example have a look at [👀 the example README with placeholders](tests/Fixtures/ExampleClassTest.README.md?plain=1).*

You must provide a fully qualified path to the method you want to copy. You can
also provide a fully qualified path to a class to copy the entire class.

### 2. Write tests for that example code

Write your tests in a separate static method in your test class. This package
can then extract the body of the method and use it as the example code.

<!-- #EXAMPLE_COPY_START = { "symbol": "\\Luttje\\ExampleTester\\Tests\\Fixtures\\ExampleClassTest", "short": false } -->

```php
final class ExampleClassTest extends TestCase
{
    public static function exampleMethod(): void
    {
        // This is an example method.
        $a = 1;
        $b = 25;

        $c = $a + $b;

        echo $c;
        // This is the end of the example method.
    }

    /**
     * @test
     */
    public function testExampleMethod(): void
    {
        ob_start();
        self::exampleMethod();
        $output = ob_get_clean();

        $this->assertSame('26', $output);
    }
}
```

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
> Document the example method here, as you normally would.
>
> **Here's the example code:**
>
> <!-- #EXAMPLE_COPY_START = { "symbol": "\\Luttje\\ExampleTester\\Tests\\Fixtures\\ExampleClassTest::exampleMethod" } -->
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

*For a full example have a look at [🏗 the compiled example README](tests/Fixtures/ExampleClassTest.README.expected.md?plain=1).*

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

## Advanced usage

You've seen the simple syntax to mark the start of an example, but you can also provide some extra options to the `#EXAMPLE_COPY_START` comment in the form of a JSON object:

```html
<!-- #EXAMPLE_COPY_START = { "symbol": "\\Luttje\\ExampleTester\\Tests\\Fixtures\\ExampleClassTest::exampleMethod" } -->
```

Is equivalent to this simpler syntax:

```html
<!-- #EXAMPLE_COPY_START = \Luttje\ExampleTester\Tests\Fixtures\ExampleClassTest::exampleMethod -->
```

However providing a JSON object unlocks the ability to configure additional properties, like the `short` property.

### The `short` property

The `short` property defaults to `true` and ensures only the body of the method or class is copied into the README.md file.

#### Setting `short` to `false` for a method
>
> Setting the `short` property to `false` will also copy the entire method signature into the README.md file for this result:
>
> <!-- #EXAMPLE_COPY_START = { "symbol": "\\Luttje\\ExampleTester\\Tests\\Fixtures\\ExampleClassTest::exampleMethod", "short": false } -->
>
> ```php
> public static function exampleMethod(): void
> {
>     // This is an example method.
>     $a = 1;
>     $b = 25;
>
>     $c = $a + $b;
>
>     echo $c;
>     // This is the end of the example method.
> }
> ```
>
> <!-- #EXAMPLE_COPY_END -->

#### Setting `short` to `false` for a class
>
> For classes it may make more sense to set `short` to `false` to copy the entire class into the README.md file for this result:
>
> <!-- #EXAMPLE_COPY_START = { "symbol": "\\Luttje\\ExampleTester\\Tests\\Fixtures\\ExampleClassTest", "short": false } -->
>
> ```php
> final class ExampleClassTest extends TestCase
> {
>     public static function exampleMethod(): void
>     {
>         // This is an example method.
>         $a = 1;
>         $b = 25;
>
>         $c = $a + $b;
>
>         echo $c;
>         // This is the end of the example method.
>     }
>
>     /**
>      * @test
>      */
>     public function testExampleMethod(): void
>     {
>         ob_start();
>         self::exampleMethod();
>         $output = ob_get_clean();
>
>         $this->assertSame('26', $output);
>     }
> }
> ```
>
> <!-- #EXAMPLE_COPY_END -->

## Ignoring examples

Especially for the readme in the root of this package, we want to selectively ignore examples. This can be done by adding `<!-- #EXAMPLE_COPY_IGNORE_START -->` and `<!-- #EXAMPLE_COPY_IGNORE_END -->` comments around the examples you want to ignore.

<!-- #EXAMPLE_COPY_IGNORE_START -->

### Ignored example

This example will be ignored.

<!-- #EXAMPLE_COPY_START = { "symbol": "\\Luttje\\ExampleTester\\Tests\\Fixtures\\ExampleClassTest", "short": false } -->

Should be removed.

<!-- #EXAMPLE_COPY_END -->

<!-- #EXAMPLE_COPY_IGNORE_END -->

### Command-line interface

The command has the following signature:

```bash
compile [--output OUTPUT] [--input INPUT]
```

**You must run the command from the root of your project.** That way the command
can find the vendor directory containing the composer autoload file.

### `--output`

The path to the README file to write the compiled examples to. Defaults to `README.md` in the root of your project.

```bash
vendor/bin/example-tester compile --output DOCS.md
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
