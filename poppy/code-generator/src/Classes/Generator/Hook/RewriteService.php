<?php

declare(strict_types = 1);

namespace Poppy\CodeGenerator\Classes\Generator\Hook;

use Poppy\CodeGenerator\Classes\Ast;

class RewriteService
{
    private Ast $ast;

    public function __construct()
    {
        $this->ast = new Ast();
    }

    public function rewrite(string $filePath, array $formClasses): void
    {
        $contents = file_get_contents($filePath);

        file_put_contents($filePath, $this->ast->generate($contents, new Visitor($formClasses)));
    }
}