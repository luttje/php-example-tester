<?php

namespace Luttje\ExampleTester\Extractor;

use ColinODell\Indentation\Indentation;
use Luttje\ExampleTester\Extractor\Visitors\ClassExtractorVisitor;
use Luttje\ExampleTester\Extractor\Visitors\MethodExtractorVisitor;
use PhpParser\Node\Stmt\Class_;
use PhpParser\Node\Stmt\ClassMethod;

class CodeExtractor implements CodeExtractorInterface
{
    private function parseFullyQualifiedName(string $fullyQualifiedName): array
    {
        $parts = explode('::', $fullyQualifiedName);

        if (count($parts) === 1) {
            $reflectionClass = new \ReflectionClass($parts[0]);

            return [$reflectionClass, null];
        }

        $reflectionClass = new \ReflectionClass($parts[0]);
        $methodName = $parts[1];

        return [$reflectionClass, $methodName];
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

    private function getMethodNode(string $code, string $methodName): ?ClassMethod
    {
        [$parser, $lexer] = self::makeParserWithLexer();
        $traverser = new \PhpParser\NodeTraverser();

        $visitor = new MethodExtractorVisitor($methodName);
        $traverser->addVisitor($visitor);

        $stmts = $parser->parse($code);

        $traverser->traverse($stmts);

        return $visitor->getMethodNode();
    }

    private function getRelevantFileCode(\ReflectionClass $reflectionClass, ?string $methodName = null)
    {
        if ($methodName !== null) {
            $reflectionMethod = $reflectionClass->getMethod($methodName);
            $fileName = $reflectionMethod->getFileName();
        } else {
            $fileName = $reflectionClass->getFileName();
        }

        $code = file_get_contents($fileName);

        return $code;
    }

    public function extractMethodDefinition(string $fullyQualifiedMethodName): string
    {
        [$reflectionClass, $methodName] = $this->parseFullyQualifiedName($fullyQualifiedMethodName);

        $codeFile = $this->getRelevantFileCode($reflectionClass, $methodName);
        $methodNode = $this->getMethodNode($codeFile, $methodName);

        if ($methodNode === null) {
            throw new \Exception("Method {$fullyQualifiedMethodName} not found");
        }

        $startLine = $methodNode->getAttribute('startLine');
        $endLine = $methodNode->getAttribute('endLine');

        // If the method has a doc comment, get the line of the doc comment instead
        if (count($methodNode->getComments()) > 0) {
            $startLine = $methodNode->getComments()[0]->getStartLine();
        }

        $splitCode = explode("\n", $codeFile);
        $code = '';

        foreach (range($startLine, $endLine) as $lineNumber) {
            $code .= $splitCode[$lineNumber - 1] . "\n";
        }

        $code = ltrim(rtrim(Indentation::unindent($code)));

        return Indentation::unindent($code);
    }

    public function extractMethodBody(string $fullyQualifiedMethodName): string
    {
        [$reflectionClass, $methodName] = $this->parseFullyQualifiedName($fullyQualifiedMethodName);

        $codeFile = $this->getRelevantFileCode($reflectionClass, $methodName);
        $methodNode = $this->getMethodNode($codeFile, $methodName);

        if ($methodNode === null) {
            throw new \Exception("Method {$fullyQualifiedMethodName} not found");
        }

        if ($methodNode->stmts === null) {
            throw new \Exception("Method {$fullyQualifiedMethodName} has no body");
        }

        // Get the line of the first statement in the method
        $firstStatement = $methodNode->stmts[0];

        // If it has comments, get the line of the first comment instead
        if (count($firstStatement->getComments()) > 0) {
            $firstStatement = $firstStatement->getComments()[0];
        }

        $firstStatementLine = $firstStatement->getStartLine();

        // Get the line of the last statement in the method
        $lastStatement = $methodNode->stmts[count($methodNode->stmts) - 1];
        $lastStatementLine = $lastStatement->getEndLine();

        $splitCode = explode("\n", $codeFile);

        $code = '';

        foreach (range($firstStatementLine, $lastStatementLine) as $lineNumber) {
            $code .= $splitCode[$lineNumber - 1] . "\n";
        }

        $code = ltrim(rtrim(Indentation::unindent($code)));

        return Indentation::unindent($code);
    }

    private function getClassNode(string $code, string $shortClassName): ?Class_
    {
        [$parser, $lexer] = self::makeParserWithLexer();
        $traverser = new \PhpParser\NodeTraverser();

        $visitor = new ClassExtractorVisitor($shortClassName);
        $traverser->addVisitor($visitor);

        $stmts = $parser->parse($code);

        $traverser->traverse($stmts);

        return $visitor->getClassNode();
    }

    public function extractClassDefinition(string $fullyQualifiedClassName): string
    {
        [$reflectionClass] = $this->parseFullyQualifiedName($fullyQualifiedClassName);

        $codeFile = $this->getRelevantFileCode($reflectionClass);
        $classNode = $this->getClassNode($codeFile, $reflectionClass->getShortName());

        if ($classNode === null) {
            throw new \Exception("Class {$fullyQualifiedClassName} not found");
        }

        $startLine = $classNode->getAttribute('startLine');
        $endLine = $classNode->getAttribute('endLine');

        // If the class has a doc comment, get the line of the doc comment instead
        if (count($classNode->getComments()) > 0) {
            $startLine = $classNode->getComments()[0]->getStartLine();
        }

        $splitCode = explode("\n", $codeFile);
        $code = '';

        foreach (range($startLine, $endLine) as $lineNumber) {
            $code .= $splitCode[$lineNumber - 1] . "\n";
        }

        $code = ltrim(rtrim(Indentation::unindent($code)));

        return Indentation::unindent($code);
    }

    public function extractClassBody(string $fullyQualifiedClassName): string
    {
        [$reflectionClass] = $this->parseFullyQualifiedName($fullyQualifiedClassName);

        $codeFile = $this->getRelevantFileCode($reflectionClass);
        $classNode = $this->getClassNode($codeFile, $reflectionClass->getShortName());

        if ($classNode === null) {
            throw new \Exception("Class {$fullyQualifiedClassName} not found");
        }

        if ($classNode->stmts === null) {
            throw new \Exception("Class {$fullyQualifiedClassName} has no body");
        }

        // Get the line of the first statement in the class
        $firstStatement = $classNode->stmts[0];

        // If it has comments, get the line of the first comment instead
        if (count($firstStatement->getComments()) > 0) {
            $firstStatement = $firstStatement->getComments()[0];
        }

        $firstStatementLine = $firstStatement->getStartLine();

        // Get the line of the last statement in the class
        $lastStatement = $classNode->stmts[count($classNode->stmts) - 1];
        $lastStatementLine = $lastStatement->getEndLine();

        $splitCode = explode("\n", $codeFile);

        $code = '';

        foreach (range($firstStatementLine, $lastStatementLine) as $lineNumber) {
            $code .= $splitCode[$lineNumber - 1] . "\n";
        }

        $code = ltrim(rtrim(Indentation::unindent($code)));

        return Indentation::unindent($code);
    }
}
