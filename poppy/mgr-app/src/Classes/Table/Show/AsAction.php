<?php

namespace Poppy\MgrApp\Classes\Table\Show;

use Closure;
use Poppy\MgrApp\Classes\Table\Render\GridActions;

trait AsAction
{
    /**
     * 调用 Actions 预览定义的操作
     */
    public function asAction(Closure $cb): self
    {
        $name       = $this->name;
        $this->type = 'action';
        return $this->display(function ($value) use ($cb, $name) {
            $render = new GridActions($value, $this, $name);
            return $render->render($cb);
        });
    }

    public function asTableAction($disabled = [])
    {
        $this->type     = 'table-action';
        $this->editable = array_diff(['add', 'down', 'up', 'delete', 'copy'], $disabled);
    }
}
