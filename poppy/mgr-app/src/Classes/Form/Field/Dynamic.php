<?php

namespace Poppy\MgrApp\Classes\Form\Field;

use Poppy\MgrApp\Classes\Form\FormItem;
use Poppy\MgrApp\Classes\Form\Traits\UseDynamicAttr;

class Dynamic extends FormItem
{

    use UseDynamicAttr;

    public function __construct(string $name, string $label)
    {
        parent::__construct($name, $label);
        $this->dynamicAttrInit();
    }

    /**
     * 关联参数, 关联参数实现参数的顺序传递, 并非根据指定的 kv 来进行数据传输
     * @param array $model
     * @return self
     */
    public function rel(array $model = []): self
    {
        $this->dynamicSetAttribute('rel', $model);
        return $this;
    }


    public function struct(): array
    {
        return array_merge([
            'dynamic' => $this->dynamicAttr(),
        ], parent::struct());
    }
}
