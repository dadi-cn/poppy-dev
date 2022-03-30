<?php

namespace Poppy\Core\Services\Factory;

use Illuminate\Support\Str;
use Poppy\Core\Classes\Traits\CoreTrait;
use Poppy\Core\Services\Contracts\ServiceArray;
use Poppy\Core\Services\Contracts\ServiceForm;
use Poppy\Core\Services\Contracts\ServiceHtml;
use Poppy\Framework\Exceptions\ApplicationException;

/**
 * 服务工厂
 */
class ServiceFactory
{
    use CoreTrait;

    /**
     * 钩子
     * @param string $id 钩子标示符
     * @param array $params 参数
     * @return null
     */
    public function parse(string $id, array $params = [])
    {
        $service = $this->coreModule()->services()->get($id);
        if (!$service) {
            return null;
        }
        $hooks  = $this->coreModule()->hooks()->get($id);
        $method = 'parse' . Str::studly($service['type']);
        if (!$hooks) {
            return null;
        }

        if (is_callable([$this, $method])) {
            return $this->$method($hooks, $params);
        }
        return null;
    }

    /**
     * 分析数组
     * @param array $hooks Hook
     * @param array $params 参数
     * @return array
     * @throws ApplicationException
     */
    protected function parseArray(array $hooks, $params = []): array
    {
        $collect = [];
        collect($hooks)->each(function ($hook) use (&$collect) {
            if (!class_exists($hook)) {
                throw new ApplicationException('Hook Class `' . $hook . '` not exist!');
            }
            $obj = new $hook();
            if ($obj instanceof ServiceArray) {
                $collect[$obj->key()] = $obj->data();
            }
        });

        return $collect;
    }

    /**
     * 解析 Html, 多组
     * @param array $hooks 钩子
     * @param array $params 参数
     * @return string
     */
    protected function parseHtml(array $hooks, $params = [])
    {
        $collect = '';
        collect($hooks)->each(function ($hook) use (&$collect) {
            if (class_exists($hook)) {
                $obj = new $hook();
                if ($obj instanceof ServiceHtml) {
                    $collect .= $obj->output();
                }
            }
        });

        return $collect;
    }

    /**
     * 分析表单
     * @param string $builder 构建器
     * @param array $params 参数
     * @return \Illuminate\Support\HtmlString|mixed
     */
    protected function parseForm($builder, $params)
    {
        if (class_exists($builder)) {
            $obj = new $builder();
            if ($obj instanceof ServiceForm) {
                return $obj->builder($params);
            }
        }

        return \Form::text($params['name'], $params['value'], $params['options'] + ['class' => 'layui-input']);
    }
}