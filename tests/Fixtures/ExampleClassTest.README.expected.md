# Example README.md

This is an example README.md file. It is used to test the merging of
examples (from the tests) into this file.

## Examples

### `exampleMethod`

This is an example description.
It can be multiple lines long and **formatted**.

<!-- #EXAMPLE_COPY_START = { "symbol": "\\Luttje\\ExampleTester\\Tests\\Fixtures\\ExampleClassTest::exampleMethod" } -->

```php
// This is an example method.
$a = 1;
$b = 25;

$c = $a + $b;

echo $c;
// This is the end of the example method.
```

<!-- #EXAMPLE_COPY_END -->

*🤓 Yay calculations!*

## Full example

An entire class body can be copied into the README.md file too!

<!-- #EXAMPLE_COPY_START = { "symbol": "\\Luttje\\ExampleTester\\Tests\\Fixtures\\ExampleClassTest" } -->

```php
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
```

<!-- #EXAMPLE_COPY_END -->

### The `short` property (default `true`)

Setting the `short` property to `false` will copy the entire class (`class Name { ... }`) or entire function (`function name() { ... }`) into the README.md file.

#### Example method (`short` set to `false`)

Setting the `short` property to `false` will also copy the entire method into the README.md file. Check it out in this quote block:

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

#### Example class (`short` set to `false`)

For classes it may make sense to set `short` to `false` to copy the entire class into the README.md file. Check it out in this quote block:

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

## License

This is where the license goes.
