<?php

namespace Poppy\MgrPage\Classes\Grid\Displayer;

use Illuminate\Support\Str;

class SwitchDisplay extends AbstractDisplayer
{

    protected $states = [
        '1' => '开',
        '0' => '关',
    ];

    public function display($states = []): string
    {
        if ($states) {
            $this->states = $states;
        }
        $type = '1/0';
        if (isset($this->states['Y'])) {
            $type = 'Y/N';
        }
        $name = $this->column->name;

        if ($type === 'Y/N') {
            $checked = $this->value === 'Y' ? 'checked' : '';
        }
        else {
            $checked = $this->value ? 'checked' : '';
        }


        $id = Str::random();
        return <<<EOT
    <div class="layui-field-checkbox-item">
        <input type="checkbox" class="layui-field-checkbox" lay-ignore $checked id="$id" data-field="$name" data-type="$type" lay-event="switch" />
        <label class="layui-field-checkbox-label" for="$id" style="margin:0;top:-3px;"></label>
    </div>
EOT;
    }

}
