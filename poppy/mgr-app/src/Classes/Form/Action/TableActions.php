<?php

namespace Poppy\MgrApp\Classes\Form\Action;

use Closure;
use Illuminate\Contracts\Support\Jsonable;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Support\Fluent;
use Poppy\MgrApp\Classes\Action\Action;
use Poppy\MgrApp\Classes\Form\Traits\UseTableAction;
use Poppy\MgrApp\Classes\Traits\UseActions;

class TableActions implements Renderable
{

    use UseActions, UseTableAction;

    protected string $type = 'actions';

    /**
     * 渲染表格数据
     */
    public function render($callback = null): Jsonable
    {
        if ($callback instanceof Closure) {
            // 绑定当前对象到 Closure, 并且将当前对象作为参数传入
            $callback->call($this, $this);
        }

        $actions = [];
        foreach ($this->items as $append) {
            if ($append instanceof Action) {
                $def       = $append->struct();
                $actions[] = $def;
            }
        }

        return new Fluent($actions);
    }
}
