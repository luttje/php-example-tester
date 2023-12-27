<?php

namespace Luttje\ExampleTester\Tests\Fixtures;

final class AlternativeStylingClass
{
    public static function methodWithBracesOnSameLine(): void {
        // start
        $a = 1;
        $b = 25;

        $c = $a + $b;
        echo $c;
        // end
    }

    public static function methodWithBracesOnNewLine()
    {
        // start
        $a = 1;
        $b = 25;

        $c = $a + $b;
        echo $c;
        // end
    }

    public static function methodWithBracesOnNewLineAndReturnType(): void
    {
        // start
        $a = 1;
        $b = 25;

        $c = $a + $b;
        echo $c;
        // end
    }

    /**
     * A multi-line doc comment
     * with a return type
     *
     * @return void
     */
    public static function methodWithBracesOnNewLineAndReturnTypeWithDocComment(): void
    {
        // start
        $a = 1;
        $b = 25;

        $c = $a + $b;
        echo $c;
        // end
    }

    public static function methodWithParametersOnNewLine(
        int $a,
        int $b
    ): void {
        // start
        $c = $a + $b;
        echo $c;
        // end
    }

    public static function methodWithParametersOnNewLineAndReturnType(
        int $a,
        int $b
    ): void {
        // start
        $c = $a + $b;
        echo $c;
        // end
    }

    public static function methodWithParametersOnNewLineAndReturnTypeAndBracesOnNewLine(
        int $a,
        int $b
    ): void
    {
        // start
        $c = $a + $b;
        echo $c;
        // end
    }

    public static function methodWithParametersOnNewLineAndBracesOnNewLine(
        int $a,
        int $b
    )
    {
        // start
        $c = $a + $b;
        echo $c;
        // end
    }

    public static function methodWithParametersOnNewLineAndBracesOnNewLineAndDefaultStringParameter(
        int $a,
        string $b = 'default with { and }'
    )
    {
        // start
        $c = $a + $b;
        echo $c;
        // end
    }

    public static function methodWithParametersOnNewLineAndBracesOnNewLineAndCommentWithBraces(
        // A comment containing { and }
        int $a,
        string $b = 'default with { and }'
    )
    {
        // start
        $c = $a + $b;
        echo $c;
        // end
    }
}
