<?php

declare(strict_types = 1);

namespace Poppy\CodeGenerator\Classes\Generator\Hook;

use Illuminate\Support\Arr;
use Poppy\CodeGenerator\Classes\CopyService;
use Poppy\CodeGenerator\Classes\Generator\Form\FormService;
use Poppy\Core\Services\Contracts\ServiceArray;
use ReflectionClass;

class HookService
{
    private CopyService $copyService;

    private const ARRAY_SERVICE_METHOD = 'data';

    private RewriteService $rewriteService;

    public function __construct(CopyService $copyService)
    {
        $this->copyService    = $copyService;
        $this->rewriteService = new RewriteService();
    }

    /**
     * @param string $clazz
     * @return bool
     * @throws \ReflectionException
     */
    public function handle(string $clazz): bool
    {
        $reflectionClass = new ReflectionClass($clazz);

        $sourceFilePath = $reflectionClass->getFileName();

        $formClasses = $this->processFormClass($reflectionClass);

        if (count($formClasses) <= 0) {
            return false;
        }

        $this->hookServiceProcess($formClasses, $sourceFilePath);

        return true;
    }

    /**
     * @param array  $formClasses
     * @param string $sourceFilePath
     * @return void
     */
    protected function hookServiceProcess(array $formClasses, string $sourceFilePath): void
    {
        $dstFilePath = $this->copyService->copy($sourceFilePath);

        $this->rewriteService->rewrite($dstFilePath, $formClasses);
    }

    /**
     * @param ReflectionClass $reflectionClass
     * @return array
     * @throws \ReflectionException
     */
    protected function processFormClass(ReflectionClass $reflectionClass): array
    {
        if ($reflectionClass->isSubclassOf(ServiceArray::class)
            && $reflectionClass->hasMethod(self::ARRAY_SERVICE_METHOD)) {

            $result = (array) $reflectionClass->getMethod(self::ARRAY_SERVICE_METHOD)
                ->invoke($reflectionClass->newInstance());

            $forms = (array) Arr::get($result, 'forms');

            (new FormService($forms))->handle();

            return $forms;
        }

        return [];
    }
}