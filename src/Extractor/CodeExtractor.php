<?php

namespace Luttje\ExampleTester\Extractor;

use ColinODell\Indentation\Indentation;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\PrettyPrinter;

class CodeExtractor implements CodeExtractorInterface
{
    private const PHP_PARSER_OPEN_TAG = "<?php\n";

    private function parseFullyQualifiedMethodName(string $fullyQualifiedMethodName): array
    {
        $parts = explode('::', $fullyQualifiedMethodName);

        if (count($parts) !== 2) {
            throw new \Exception("Invalid fully qualified method name: {$fullyQualifiedMethodName}");
        }

        return $parts;
    }

    private static function makeParserWithLexer()
    {
        $lexer = new \PhpParser\Lexer\Emulative([
            'usedAttributes' => [
                'comments',
                'startLine', 'endLine',
                'startTokenPos', 'endTokenPos',
            ],
        ]);
        $parser = new \PhpParser\Parser\Php7($lexer);

        return [$parser, $lexer];
    }

    private function getMethodNodeWithFormatter(string $fullyQualifiedMethodName, ?\Closure &$formatter = null): ClassMethod
    {
        [$className, $methodName] = $this->parseFullyQualifiedMethodName($fullyQualifiedMethodName);

        $reflectionClass = new \ReflectionClass($className);
        $reflectionMethod = $reflectionClass->getMethod($methodName);
        $fileName = $reflectionMethod->getFileName();

        [$parser, $lexer] = self::makeParserWithLexer();
        $traverser = new \PhpParser\NodeTraverser();

        $visitor = new MethodExtractorVisitor($className, $methodName);
        $traverser->addVisitor($visitor);

        $code = file_get_contents($fileName);

        $oldStmts = $parser->parse($code);
        $oldTokens = $lexer->getTokens();

        $traverser->traverse($oldStmts);

        $methodNode = $visitor->getMethodNode();

        if ($methodNode === null) {
            throw new \Exception("Could not find method {$fullyQualifiedMethodName}");
        }

        $prettyPrinter = new PrettyPrinter\Standard();
        // Replace "<?php\n" from the start of the code with an empty string
        $formatter = fn ($newStmts) => substr($prettyPrinter->printFormatPreserving($newStmts, $oldStmts, $oldTokens), strlen(static::PHP_PARSER_OPEN_TAG));

        return $methodNode;
    }

    public function extractMethodBody(string $fullyQualifiedMethodName): string
    {
        $methodNode = $this->getMethodNodeWithFormatter($fullyQualifiedMethodName, $formatter);
        $code = $formatter($methodNode->stmts);

        $code = ltrim(rtrim(Indentation::unindent($code)));

        return Indentation::unindent($code);
    }

    public function extractMethodDefinition(string $fullyQualifiedMethodName): string
    {
        $methodNode = $this->getMethodNodeWithFormatter($fullyQualifiedMethodName, $formatter);
        $code = $formatter([$methodNode]);

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
