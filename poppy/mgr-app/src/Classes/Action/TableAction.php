<?php

namespace Poppy\MgrApp\Classes\Action;

/**
 * 网页预览(Iframe)
 */
final class TableAction extends Action
{

    /**
     * 表格操作
     * @var string
     */
    private string $action = '';

    public function action($action): self
    {
        $this->action = $action;
        return $this;
    }

    /**
     * Action  渲染
     * @return array
     */
    public function struct(): array
    {
        return array_merge(parent::struct(), [
            'method' => 'table',
            'action' => $this->action,
        ]);
    }
}
