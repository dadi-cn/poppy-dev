<?php

namespace Poppy\MgrPage\Classes\Form\Field;

use Poppy\MgrPage\Classes\Form\Field;
use Poppy\System\Models\SysConfig;

class SwitchField extends Field
{

    protected $default = 0;

    public function render()
    {
        $this->options = [
            SysConfig::NO  => '关闭',
            SysConfig::YES => '开启',
        ];
        return parent::render();
    }
}
