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

### 1. Prepare your README

Add the start and end comments to your README where you want the examples to appear:

```html
<!-- #EXAMPLES_START -->

Anything here will be replaced with the examples.

<!-- #EXAMPLES_END -->
```

*For a full example have a look at [👀 the example README with placeholders](tests/Fixtures/ExampleClassTest.README.md).*

### 2. Write annotated tests for example code

Add PHP (8+) attributes to methods of which the content should be included in your README.
Then run the `example-tester` command to compile the examples into your README.

```php
<?php

use Luttje\ExampleTester\Attributes\ReadmeExample;
use Luttje\ExampleTester\Attributes\ReadmeExampleDescription;
use Luttje\ExampleTester\Tests\TestCase;

final class ExampleClassTest extends TestCase
{
    #[ReadmeExample('`exampleMethod`')]
    #[ReadmeExampleDescription(<<<'MD'
        This is an example description.
        It can be multiple lines long and **formatted**.
    MD, '*🤓 Yay calculations!*')]
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

Add the `#[ReadmeExample(string)]` attribute to your test methods and provide it
with the title for the example. This title will be used in the README.

You can provide any amount of `#[ReadmeExampleDescription(string, string)]`
attributes to add descriptions to your examples. The second string is optional and
can be used to provide footer text that will appear below the example.

### 3. Compile the examples into your README

Run the `example-tester` command to compile the examples into your README using
the `vendor/bin/example-tester compile` command.

```bash
vendor/bin/example-tester compile \\Your\\Autoloaded\\Namespace\\
```

You must provide a namespace to the command to tell the compiler where to look
for your tests. **The namespace must be autoloaded by composer using the PSR-4
standard.**

After running the command, the examples will be compiled into your README. This is what the generated section may look like:

> <!-- #EXAMPLES_START -->
>
> ### `exampleMethod`
>
> This is an example description.
> It can be multiple lines long and **formatted**.
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
> *🤓 Yay calculations!*
>
> <!-- #EXAMPLES_END -->

*For a full example have a look at [🏗 the compiled example README](tests/Fixtures/ExampleClassTest.README.expected.md).*

> [!Note]
> You can make your workflow even smoother by adding the `example-tester compile`
> command to your `composer.json` scripts. That way you can run it with
> `composer compile-readme`.

```json
{
    "scripts": {
        "compile-readme": [
            "vendor/bin/example-tester compile \\Your\\Autoloaded\\Namespace\\"
        ]
    }
}
```

## Options

The command has the following signature:

```bash
compile [--output OUTPUT] [--input INPUT] [--warning-comment WARNING-COMMENT] [--] <namespace>
```

**You must run the command from the root of your project.** That way the command
can find the vendor directory containing the autoload file.

### `--output`

The path to the README file to write the compiled examples to. Defaults to `README.md` in the root of your project.

```bash
vendor/bin/example-tester compile --output DOCS.md \\Your\\Autoloaded\\Namespace\\
```

### `--input`

The path to the README file to read the examples from. Defaults to the `--output` path.

### `--warning-comment`

The path to a file containing a comment that will be added to the top of the
compiled README section (right after the `<!-- #EXAMPLES_START -->` comment).
This can be used to warn users that the section is automatically generated and
should not be edited manually.

Set this to `false` to disable the warning comment:

```bash
vendor/bin/example-tester compile --warning-comment=false \\Your\\Autoloaded\\Namespace\\
```

*Defaults to [the warning text in `CompileReadmeCommand`](src/Commands/CompileReadmeCommand.php).*

## Testing

Run the tests with:

```bash
composer test
```

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
