<?php

namespace Poppy\MgrPage\Classes\Grid\Displayer;

class ProgressBar extends AbstractDisplayer
{
    public function display()
    {
        $this->value = (int) $this->value;
        return <<<EOT
<div class="layui-progress" style="margin-top: 12px;">
    <div class="layui-progress-bar layui-bg-blue" style="width: $this->value%;">
    <span class="layui-progress-text">$this->value%</span>
    </div>
</div>
EOT;
    }
}
