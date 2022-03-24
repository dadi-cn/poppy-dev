<?php

namespace Poppy\MgrApp\Classes\Form\Field;

use Poppy\MgrApp\Classes\Form\FormItem;
use Poppy\MgrApp\Classes\Form\Traits\UseOptions;

class Radio extends FormItem
{
    use UseOptions;

    /**
     * 设置为按钮样式
     * @return $this
     */
    public function button(): self
    {
        $this->setAttribute('button', true);
        return $this;
    }
}
