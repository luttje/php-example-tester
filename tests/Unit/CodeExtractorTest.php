<?php

namespace Luttje\ExampleTester\Tests\Unit;

use Luttje\ExampleTester\Extractor\CodeExtractor;
use Luttje\ExampleTester\Tests\Fixtures\AlternativeStylingClass;
use Luttje\ExampleTester\Tests\TestCase;

/**
 * @group code-extractor
 */
final class CodeExtractorTest extends TestCase
{
    public function testMethodWithBracesOnSameLine()
    {
        $codeExtractor = new CodeExtractor();
        $code = $codeExtractor->extractMethodBody(AlternativeStylingClass::class . '::methodWithBracesOnSameLine');

        $this->assertSame(<<<'CODE'
        // start
        $a = 1;
        $b = 25;

        $c = $a + $b;
        echo $c;
        // end
        CODE, $code);
    }

    public function testMethodWithBracesOnSameLineDefinitionDocComment()
    {
        $codeExtractor = new CodeExtractor();
        $code = $codeExtractor->extractMethodDefinition(AlternativeStylingClass::class . '::methodWithBracesOnSameLine');

        $this->assertSame(<<<'CODE'
        /**
         * A method with braces on the same line
         */
        public static function methodWithBracesOnSameLine(): void {
            // start
            $a = 1;
            $b = 25;

            $c = $a + $b;
            echo $c;
            // end
        }
        CODE, $code);
    }

    public function testMethodWithBracesOnNewLine()
    {
        $codeExtractor = new CodeExtractor();
        $code = $codeExtractor->extractMethodBody(AlternativeStylingClass::class . '::methodWithBracesOnNewLine');

        $this->assertSame(<<<'CODE'
        // start
        $a = 1;
        $b = 25;

        $c = $a + $b;
        echo $c;
        // end
        CODE, $code);
    }

    public function testMethodWithBracesOnNewLineAndReturnType()
    {
        $codeExtractor = new CodeExtractor();
        $code = $codeExtractor->extractMethodBody(AlternativeStylingClass::class . '::methodWithBracesOnNewLineAndReturnType');

        $this->assertSame(<<<'CODE'
        // start
        $a = 1;
        $b = 25;

        $c = $a + $b;
        echo $c;
        // end
        CODE, $code);
    }

    public function testMethodWithBracesOnNewLineAndReturnTypeWithDocComment()
    {
        $codeExtractor = new CodeExtractor();
        $code = $codeExtractor->extractMethodBody(AlternativeStylingClass::class . '::methodWithBracesOnNewLineAndReturnTypeWithDocComment');

        $this->assertSame(<<<'CODE'
        // start
        $a = 1;
        $b = 25;

        $c = $a + $b;
        echo $c;
        // end
        CODE, $code);
    }

    public function testMethodWithParametersOnNewLine()
    {
        $codeExtractor = new CodeExtractor();
        $code = $codeExtractor->extractMethodBody(AlternativeStylingClass::class . '::methodWithParametersOnNewLine');

        $this->assertSame(<<<'CODE'
        // start
        $c = $a + $b;
        echo $c;
        // end
        CODE, $code);
    }

    public function testMethodWithParametersOnNewLineAndReturnType()
    {
        $codeExtractor = new CodeExtractor();
        $code = $codeExtractor->extractMethodBody(AlternativeStylingClass::class . '::methodWithParametersOnNewLineAndReturnType');

        $this->assertSame(<<<'CODE'
        // start
        $c = $a + $b;
        echo $c;
        // end
        CODE, $code);
    }

    public function testMethodWithParametersOnNewLineAndReturnTypeAndBracesOnNewLine()
    {
        $codeExtractor = new CodeExtractor();
        $code = $codeExtractor->extractMethodBody(AlternativeStylingClass::class . '::methodWithParametersOnNewLineAndReturnTypeAndBracesOnNewLine');

        $this->assertSame(<<<'CODE'
        // start
        $c = $a + $b;
        echo $c;
        // end
        CODE, $code);
    }

    public function testMethodWithParametersOnNewLineAndBracesOnNewLine()
    {
        $codeExtractor = new CodeExtractor();
        $code = $codeExtractor->extractMethodBody(AlternativeStylingClass::class . '::methodWithParametersOnNewLineAndBracesOnNewLine');

        $this->assertSame(<<<'CODE'
        // start
        $c = $a + $b;
        echo $c;
        // end
        CODE, $code);
    }

    public function testMethodWithParametersOnNewLineAndBracesOnNewLineAndDefaultStringParameter()
    {
        $codeExtractor = new CodeExtractor();
        $code = $codeExtractor->extractMethodBody(AlternativeStylingClass::class . '::methodWithParametersOnNewLineAndBracesOnNewLineAndDefaultStringParameter');

        $this->assertSame(<<<'CODE'
        // start
        $c = $a + $b;
        echo $c;
        // end
        CODE, $code);
    }

    public function testMethodWithParametersOnNewLineAndBracesOnNewLineAndCommentWithBraces()
    {
        $codeExtractor = new CodeExtractor();
        $code = $codeExtractor->extractMethodBody(AlternativeStylingClass::class . '::methodWithParametersOnNewLineAndBracesOnNewLineAndCommentWithBraces');

        $this->assertSame(<<<'CODE'
        // start
        $c = $a + $b;
        echo $c;
        // end
        CODE, $code);
    }

    public function testClassWithDocComment()
    {
        $codeExtractor = new CodeExtractor();
        $code = $codeExtractor->extractClassDefinition(AlternativeStylingClass::class);

        $this->assertStringStartsWith(<<<'CODE'
        /**
         * A class with methods of alternative styling
         */
        final class AlternativeStylingClass
        {
            /**
             * A method with braces on the same line
             */
            public static function methodWithBracesOnSameLine(): void {
                // start
                $a = 1;
                $b = 25;

                $c = $a + $b;
                echo $c;
                // end
            }
        CODE, $code);
    }
}
