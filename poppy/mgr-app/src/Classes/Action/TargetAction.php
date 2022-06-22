<?php

namespace Poppy\MgrApp\Classes\Action;


/**
 * 窗口模式
 */
class TargetAction extends Action
{

    private string $target = '_blank';

    public function target($target): self
    {
        $this->target = $target;
        return $this;
    }

    /**
     * Action  渲染
     * @return array
     */
    public function struct(): array
    {
        return array_merge(parent::struct(), [
            'method' => 'target',
            'target' => $this->target,
        ]);
    }
}
