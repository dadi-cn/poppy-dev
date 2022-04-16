<?php

namespace Poppy\System\Action;

use Illuminate\Support\Str;
use Poppy\Framework\Classes\Traits\AppTrait;
use Throwable;

/**
 * Apidoc 读取
 */
class Apidoc
{

    use AppTrait;

    /**
     * @param $type
     * @return bool|string
     */
    public function local($type)
    {
        $path = 'docs/' . $type . '/assets/main.bundle.js';
        try {
            $content = app('files')->get(public_path($path));
            return $this->parseContent($content);
        } catch (Throwable $e) {
            return $this->setError('文档 ' . $path . '不存在, 请生成' . $e->getMessage());
        }
    }

    /**
     *
     * @param $content
     * @return string
     */
    private function parseContent($content): string
    {
        $jsObject = substr($content, strpos($content, '[{type:"'), strpos($content, 'generator:{name:"apidoc",time:') - strpos($content, '[{type:"'));
        return Str::beforeLast($jsObject, ';const ');
    }
}