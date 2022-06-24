<?php

namespace Poppy\MgrApp\Classes\Table\Render;

use Closure;
use Illuminate\Contracts\Support\Jsonable;
use Illuminate\Support\Fluent;
use Poppy\MgrApp\Classes\Action\Action;
use Poppy\MgrApp\Classes\Traits\UseActions;
use Poppy\MgrApp\Classes\Traits\UseInteraction;

class GridActions extends Render
{

    use UseActions, UseInteraction;


    /**
     * @inheritDoc
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

        $params = [
            'items' => $actions,
        ];
        if ($this->style) {
            $params['style'] = $this->style;
            if ($this->style === 'dropdown') {
                $params['length']        = $this->length;
                $params['dropdown-icon'] = $this->dropdownIcon;
            }
        }

        return new Fluent($params);
    }
}
