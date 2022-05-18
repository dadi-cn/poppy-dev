<?php

declare(strict_types = 1);

namespace Poppy\CodeGenerator\Classes;

use PhpParser\NodeTraverser;
use PhpParser\NodeVisitorAbstract;
use PhpParser\Parser;
use PhpParser\ParserFactory;
use PhpParser\PrettyPrinter\Standard;
use PhpParser\PrettyPrinterAbstract;

class Ast
{
    private Parser $parser;

    private PrettyPrinterAbstract $printer;

    public function __construct()
    {
        $parserFactory = new ParserFactory();

        $this->parser = $parserFactory->create(ParserFactory::PREFER_PHP7);

        $this->printer = new Standard();
    }

    public function generate(string $code, NodeVisitorAbstract $visitor): string
    {
        $stmts = $this->parser->parse($code);

        $nodeTraverser = new NodeTraverser();

        $nodeTraverser->addVisitor($visitor);

        $nodeTraverser->traverse($stmts);

        return $this->printer->prettyPrintFile($stmts);
    }
}