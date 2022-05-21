<?php

declare(strict_types = 1);

namespace Poppy\CodeGenerator\Classes\Generator\Hook;

use PhpParser\Node;
use PhpParser\NodeVisitorAbstract;
use Poppy\CodeGenerator\Classes\Constants;

class Visitor extends NodeVisitorAbstract
{
    private array $formClasses = [];

    /**
     * @param array $formClasses
     */
    public function __construct(array $formClasses)
    {
        $this->formClasses = $formClasses;
    }

    public function leaveNode(Node $node): ?Node
    {
        switch (true) {
            case $node instanceof Node\Stmt\Namespace_:
                $this->handleNamespace($node);
                break;
            case $node instanceof Node\Stmt\Use_:
                $this->handleUses($node);
                break;
        }

        return $node;
    }

    private function handleNamespace(Node\Stmt\Namespace_ $node): void
    {
        $node->name->parts[] = Constants::APPEND_NAMESPACE;
    }

    private function handleUses(Node\Stmt\Use_ $node): void
    {
        $useUse = $node->uses[0];

        $useClass = $useUse->name->toString();
        if (!in_array($useClass, $this->formClasses, true)) {
            return;
        }

        $className = array_pop($useUse->name->parts);

        $useUse->name->parts[] = Constants::APPEND_NAMESPACE;
        $useUse->name->parts[] = $className;
    }
}