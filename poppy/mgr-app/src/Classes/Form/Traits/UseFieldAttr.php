<?php


namespace Poppy\MgrApp\Classes\Form\Traits;

use Illuminate\Support\Fluent;

/**
 * 字段属性
 */
trait UseFieldAttr
{
    /**
     * 字段的属性
     * @var Fluent
     */
    protected Fluent $fieldAttr;


    protected function fieldAttrInit()
    {
        $this->fieldAttr = new Fluent();
    }

    /**
     * 获取属性列表
     * @return object
     */
    protected function fieldAttr(): object
    {
        return (object) $this->fieldAttr->toArray();
    }

    /**
     * 字段属性
     * @param $attr
     * @param $value
     * @return $this
     */
    public function setAttribute($attr, $value): self
    {
        $this->fieldAttr->offsetSet($attr, $value);
        return $this;
    }

    /**
     * 获取属性
     * @param $attr
     * @return mixed
     */
    public function getAttribute($attr)
    {
        return $this->fieldAttr->offsetGet($attr);
    }
}
