<?php

namespace Poppy\MgrApp\Classes\Action;


/**
 * 新窗口打开
 */
class ProgressAction extends Action
{

    /**
     * Action  渲染
     * @return array
     */
    public function struct(): array
    {
        return array_merge(parent::struct(), [
            'method' => 'progress',
        ]);
    }
}
