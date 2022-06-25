<?php

namespace Poppy\MgrApp\Classes\Action;

/**
 * 请求操作
 */
final class TextAction extends Action
{
    /**
     * @var bool
     */
    private bool $copyable = false;

    /**
     * Action  渲染
     * @return array
     */
    public function struct(): array
    {
        return array_merge(parent::struct(), [
            'method'   => 'text',
            'copyable' => $this->copyable,
        ]);
    }


    /**
     * 可复制的
     * @return void
     */
    public function copyable()
    {
        $this->copyable = true;
    }
}
