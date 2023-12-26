<?php

namespace Luttje\ExampleTester\Tests\Fixtures;

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
