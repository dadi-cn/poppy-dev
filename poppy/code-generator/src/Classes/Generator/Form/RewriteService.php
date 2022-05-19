<?php

declare(strict_types = 1);

namespace Poppy\CodeGenerator\Classes\Generator\Form;

use Poppy\CodeGenerator\Classes\Ast;

class RewriteService
{
    private Ast $ast;

    /**
     */
    public function __construct()
    {
        $this->ast = new Ast();
    }

    public function rewrite(string $filePath)
    {
        $contents = file_get_contents($filePath);

        $contents = (new MethodReplacer($contents))->replace();

        $code = $this->ast->generate($contents, new Visitor());

        file_put_contents($filePath, $code);
    }
}