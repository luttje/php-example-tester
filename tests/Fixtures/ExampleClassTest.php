<?php

namespace Luttje\ExampleTester\Tests\Fixtures;

use Luttje\ExampleTester\Tests\TestCase;

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
