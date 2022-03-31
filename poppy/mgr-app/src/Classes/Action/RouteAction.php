<?php

namespace Poppy\MgrApp\Classes\Action;


/**
 * 新页面打开路由
 */
class RouteAction extends Action
{

    /**
     * Action  渲染
     * @return array
     */
    public function struct(): array
    {
        return array_merge(parent::struct(), [
            'method' => 'route',
        ]);
    }
}
