<?php

namespace Poppy\MgrApp\Classes\Grid\Tools;

use Closure;
use Illuminate\Contracts\Support\Jsonable;
use Illuminate\Support\Fluent;
use Poppy\MgrApp\Classes\Action\Action;
use Poppy\MgrApp\Classes\Contracts\Structable;
use Poppy\MgrApp\Classes\Traits\UseActions;
use Poppy\MgrApp\Classes\Traits\UseInteraction;

class Interactions implements Structable
{

    use UseActions, UseInteraction;

    /**
     *
     */
    public function struct(): array
    {
        $actions = [];
        foreach ($this->items as $append) {
            if ($append instanceof Action) {
                $def       = $append->struct();
                $actions[] = $def;
            }
        }
        return (new Fluent($actions))->toArray();
    }


    /**
     * 调用函数
     */
    public function call(Closure $callback): Jsonable
    {
        // 绑定当前对象到 Closure, 并且将当前对象作为参数传入
        $callback->call($this, $this);

        $params = [
            'items' => $this->struct(),
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
