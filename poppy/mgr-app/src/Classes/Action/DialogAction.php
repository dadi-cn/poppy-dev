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

    /**
     * 宽度
     * @var int
     */
    private int $width = 600;

    public function type($type): self
    {
        $this->render = strtolower($type);
        return $this;
    }


    /**
     * 宽度
     * @param int $width
     * @return void
     */
    public function width(int $width)
    {
        $this->width = $width;
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
            'width'  => $this->width,
        ]);
    }
}
