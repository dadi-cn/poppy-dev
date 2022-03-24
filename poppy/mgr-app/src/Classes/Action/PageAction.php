<?php

namespace Poppy\MgrApp\Classes\Action;


/**
 * 页面操作
 * 页面/抽屉方式打开
 */
class PageAction extends Action
{

    /**
     * 渲染类型, 需要明确指定 form:表单形式
     * @var string
     */
    private string $displayType = '';

    public function type($type): self
    {
        $this->displayType = strtolower($type);
        return $this;
    }

    /**
     * Action  渲染
     * @return array
     */
    public function struct(): array
    {
        return array_merge(parent::struct(), [
            'method' => 'page',
            'render' => $this->displayType,
        ]);
    }
}
