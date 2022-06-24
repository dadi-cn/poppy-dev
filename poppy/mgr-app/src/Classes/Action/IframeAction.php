<?php

namespace Poppy\MgrApp\Classes\Action;

/**
 * 网页预览(Iframe)
 */
final class IframeAction extends Action
{

    protected int $width = 400;

    /**
     * 预览
     * @param int $width 宽度
     * @return void
     */
    public function width(int $width = 400)
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
            'method' => 'iframe',
            'width'  => 400,
        ]);
    }
}
