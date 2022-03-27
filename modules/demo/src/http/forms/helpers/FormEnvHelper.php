<?php

namespace Demo\Http\Forms\Helpers;

use Poppy\Core\Classes\Inspect\CommentParser;
use Poppy\Framework\Helper\EnvHelper;
use Poppy\MgrPage\Classes\Widgets\FormWidget;
use ReflectionClass;

class FormEnvHelper extends FormWidget
{

    /**
     * 表单标题
     * @var string
     */
    protected $title = 'EnvHelper';


    /**
     * Build a form here.
     */
    public function form()
    {
        $Ref     = new ReflectionClass(EnvHelper::class);
        $methods = $Ref->getMethods();
        foreach ($methods as $method) {
            if ($method->isPublic()) {
                $methodName = $method->getName();
                $comment = $method->getDocComment();
                $DocParser = new CommentParser();
                $docs = $DocParser->parseMethod($comment);
                if (in_array($methodName, [
                    'isInternalIp',
                ])) {
                    continue;
                }
                $result = EnvHelper::$methodName();
                if (is_bool($result)) {
                    if ($result) {
                        $result = 'true';
                    }
                    else {
                        $result = 'false';
                    }
                }
                $this->display($methodName, ucfirst($methodName))->default($result)->help($docs['description']);
            }
        }
    }
}
