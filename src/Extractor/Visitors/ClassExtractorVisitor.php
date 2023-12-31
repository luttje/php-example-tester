<?php

namespace Luttje\ExampleTester\Extractor\Visitors;

use PhpParser\Node;
use PhpParser\NodeVisitorAbstract;

class ClassExtractorVisitor extends NodeVisitorAbstract
{
    private ?Node\Stmt\Class_ $classNode = null;

    /**
     * Use statements for the class.
     *
     * @var Node\Stmt\Use_[]
     */
    private array $useStatements = [];

    public function __construct(
        private string $className,
    )
    { }

    public function enterNode(Node $node)
    {
        if ($node instanceof Node\Stmt\Use_) {
            $this->useStatements[] = $node;
        }

        if ($node instanceof Node\Stmt\Class_) {
            if ($node->name->toString() === $this->className) {
                $this->classNode = $node;
            }
        }
    }

    public function getClassNode(): ?Node\Stmt\Class_
    {
        return $this->classNode;
    }

    /**
     * @return Node\Stmt\Use_[]
     */
    public function getUseStatements(): array
    {
        return $this->useStatements;
    }
}
