<?php

declare(strict_types = 1);

namespace Poppy\CodeGenerator\Classes\Generator\Form;

use Poppy\CodeGenerator\Classes\CopyService;
use ReflectionClass;
use Throwable;

class FormService
{
    private array $formClasses = [];

    private CopyService $copyService;

    private RewriteService $rewriteService;

    /**
     * @param array $formClasses
     */
    public function __construct(array $formClasses)
    {
        $this->formClasses = $formClasses;

        $this->copyService    = new CopyService();
        $this->rewriteService = new RewriteService();
    }

    public function handle()
    {
        foreach ($this->formClasses as $clazz) {
            try {
                $reflection = new ReflectionClass($clazz);

                $filePath    = $reflection->getFileName();
                $newFilePath = $this->copyService->copy($filePath);

                $this->rewriteService->rewrite($newFilePath);
            } catch (Throwable $e) {

            }
        }
    }

}