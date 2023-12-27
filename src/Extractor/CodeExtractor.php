<?php

namespace Luttje\ExampleTester\Extractor;

use ColinODell\Indentation\Indentation;

class CodeExtractor implements CodeExtractorInterface
{
    public function extractMethodBody(string $fullyQualifiedMethodName): string
    {
        $parts = explode('::', $fullyQualifiedMethodName);
        $className = $parts[0];
        $methodName = $parts[1];
        $reflectionClass = new \ReflectionClass($className);
        $reflectionMethod = $reflectionClass->getMethod($methodName);

        $fileName = $reflectionMethod->getFileName();
        $startLine = $reflectionMethod->getStartLine() - 1;
        $endLine = $reflectionMethod->getEndLine() - 1;

        $code = '';
        $hasFoundBody = false;

        foreach (range($startLine, $endLine) as $lineNumber) {
            $line = file($fileName)[$lineNumber];

            if ($hasFoundBody) {
                $code .= $line;
            } else {
                // This fails if a parameter has a default value with a curly brace in it
                if (strpos($line, '{') !== false) {
                    $hasFoundBody = true;
                }
            }
        }

        $code = rtrim(Indentation::unindent($code));

        // Remove { and } and any newlines before/after them
        $code = preg_replace('/^{[\r\n]+/', '', $code);
        $code = preg_replace('/[\r\n]+}$/', '', $code);

        return Indentation::unindent($code);
    }

    public function extractMethodDefinition(string $fullyQualifiedMethodName): string
    {
        $parts = explode('::', $fullyQualifiedMethodName);
        $className = $parts[0];
        $methodName = $parts[1];
        $reflectionClass = new \ReflectionClass($className);
        $reflectionMethod = $reflectionClass->getMethod($methodName);

        $fileName = $reflectionMethod->getFileName();
        $startLine = $reflectionMethod->getStartLine() - 1;
        $endLine = $reflectionMethod->getEndLine() - 1;

        $code = '';

        foreach (range($startLine, $endLine) as $lineNumber) {
            $code .= file($fileName)[$lineNumber];
        }

        $code = ltrim(rtrim(Indentation::unindent($code)));

        return Indentation::unindent($code);
    }

    public function extractClassBody(string $fullyQualifiedClassName): string
    {
        $reflectionClass = new \ReflectionClass($fullyQualifiedClassName);

        $fileName = $reflectionClass->getFileName();
        $startLine = $reflectionClass->getStartLine();
        $endLine = $reflectionClass->getEndLine() - 1;

        $code = '';

        foreach (range($startLine, $endLine) as $lineNumber) {
            $code .= file($fileName)[$lineNumber];
        }

        $code = ltrim(rtrim(Indentation::unindent($code)));

        // Remove { and } and any newlines before/after them
        $code = preg_replace('/^{[\r\n]+/', '', $code);
        $code = preg_replace('/[\r\n]+}$/', '', $code);

        return Indentation::unindent($code);
    }

    public function extractClassDefinition(string $fullyQualifiedClassName): string
    {
        $reflectionClass = new \ReflectionClass($fullyQualifiedClassName);

        $fileName = $reflectionClass->getFileName();
        $startLine = $reflectionClass->getStartLine() - 1;
        $endLine = $reflectionClass->getEndLine() - 1;

        $code = '';

        foreach (range($startLine, $endLine) as $lineNumber) {
            $code .= file($fileName)[$lineNumber];
        }

        $code = ltrim(rtrim(Indentation::unindent($code)));

        return Indentation::unindent($code);
    }
}
