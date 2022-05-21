<?php

declare(strict_types = 1);

namespace Poppy\CodeGenerator\Classes\Generator\Form;

use PhpParser\Node;
use PhpParser\NodeVisitorAbstract;
use Poppy\CodeGenerator\Classes\Constants;

class Visitor extends NodeVisitorAbstract
{

    private string $extendClassName;

    private string $extendClassNameSpace;

    private bool $useAdded = false;

    public function __construct()
    {
        $this->extendClassNameSpace = Constants::SETTING_EXTEND_NAMESPACE;

        $this->extendClassName = last(explode('\\', $this->extendClassNameSpace));
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
            case $node instanceof Node\Stmt\Class_:
                $this->handleClass($node);
                break;
            case $node instanceof Node\Stmt\Property:
                $this->handleProperty($node);
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
        if ($this->useAdded) {
            return;
        }

        // 增加一个Poppy\MgrApp\Classes\Form\SettingBase;
        $node->uses[] = new Node\Stmt\UseUse(new Node\Name($this->extendClassNameSpace));

        $this->useAdded = true;
    }

    private function handleClass(Node\Stmt\Class_ $node): void
    {
        $node->extends = new Node\Name($this->extendClassName);
    }

    private function handleProperty(Node\Stmt\Property $node): void
    {
        $node->flags = Node\Stmt\Class_::MODIFIER_PROTECTED;
        $node->type  = new Node\Name('string');
    }

}