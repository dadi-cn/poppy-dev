<?php

namespace Poppy\MgrApp\Classes\Action;


/**
 * 页面操作
 * 页面/抽屉方式打开
 */
class DialogAction extends Action
{

    /**
     * 渲染类型, 需要明确指定 form:表单形式
     * @var string
     */
    private string $render = '';

    public function type($type): self
    {
        $this->render = strtolower($type);
        return $this;
    }

    /**
     * Action  渲染
     * @return array
     */
    public function struct(): array
    {
        return array_merge(parent::struct(), [
            'method' => 'dialog',
            'render' => $this->render,
        ]);
    }
}
