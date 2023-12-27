<?php

namespace Luttje\ExampleTester\Extractor;

use PhpParser\Node;
use PhpParser\NodeVisitorAbstract;

/**
 * @internal
 */
class MethodExtractorVisitor extends NodeVisitorAbstract
{
    private ?Node\Stmt\ClassMethod $methodNode = null;

    public function __construct(
        private string $className,
        private string $methodName,
    )
    { }

    public function enterNode(Node $node)
    {
        if ($node instanceof Node\Stmt\ClassMethod) {
            if ($node->name->toString() === $this->methodName) {
                $this->methodNode = $node;
            }
        }
    }

    public function getMethodNode(): ?Node\Stmt\ClassMethod
    {
        return $this->methodNode;
    }
}
