<?php

namespace Luttje\ExampleTester\Tests\Fixtures;

use Luttje\ExampleTester\Attributes\ReadmeExample;
use Luttje\ExampleTester\Attributes\ReadmeExampleDescription;

final class ExampleClass
{
    #[ReadmeExample('`exampleMethod`')]
    #[ReadmeExampleDescription(<<<'MD'
        This is an example description.
        It can be multiple lines long and **formatted**.
    MD)]
    public function exampleMethod(): void
    {
        // This is an example method.
        $a = 1;
        $b = 25;

        $c = $a + $b;

        echo $c;
    }
}
