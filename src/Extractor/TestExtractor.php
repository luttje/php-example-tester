<?php

namespace Luttje\ExampleTester\Extractor;

use ColinODell\Indentation\Indentation;

class TestExtractor implements TestExtractorInterface
{
    public function extractMethodBody(string $fullyQualifiedMethodName): string
    {
        $parts = explode('::', $fullyQualifiedMethodName);
        $className = $parts[0];
        $methodName = $parts[1];
        $reflectionClass = new \ReflectionClass($className);
        $reflectionMethod = $reflectionClass->getMethod($methodName);

        $fileName = $reflectionMethod->getFileName();
        $startLine = $reflectionMethod->getStartLine();
        $endLine = $reflectionMethod->getEndLine() - 1;

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
}
