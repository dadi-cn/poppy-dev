<?php


namespace Poppy\MgrApp\Classes\Form\Traits;

use Illuminate\Support\Fluent;

/**
 * 动态表单属性
 */
trait UseDynamicAttr
{
    /**
     * 字段的属性
     * @var Fluent
     */
    protected Fluent $dynamicAttr;


    protected function dynamicAttrInit()
    {
        $this->dynamicAttr = new Fluent();
    }

    /**
     * 获取属性列表
     * @return object
     */
    protected function dynamicAttr(): object
    {
        return (object) $this->dynamicAttr->toArray();
    }

    /**
     * 字段属性
     * @param $attr
     * @param $value
     * @return $this
     */
    public function dynamicSetAttribute($attr, $value): self
    {
        $this->dynamicAttr->offsetSet($attr, $value);
        return $this;
    }

    /**
     * 获取属性
     * @param $attr
     * @return mixed
     */
    public function dynamicGetAttribute($attr)
    {
        return $this->dynamicAttr->offsetGet($attr);
    }


    /**
     * 使用网络数据加载项目
     * @param string $name 名称
     * @param string $params 参数
     * @return $this
     */
    public function depend(string $name, string $params = ''): self
    {
        $this->dynamicSetAttribute('depend', $name);
        if ($params) {
            $this->dynamicSetAttribute('depend-params', $params);
        }
        return $this;
    }
}
