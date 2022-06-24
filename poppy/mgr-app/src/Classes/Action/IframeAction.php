<?php

namespace Poppy\MgrApp\Classes\Action;

/**
 * 网页预览(Iframe)
 */
final class IframeAction extends Action
{

    /**
     * Action  渲染
     * @return array
     */
    public function struct(): array
    {
        return array_merge(parent::struct(), [
            'method' => 'iframe',
        ]);
    }
}
