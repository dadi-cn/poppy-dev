<?php

namespace Demo\App\Forms;

use Poppy\Framework\Classes\Resp;
use Poppy\MgrApp\Classes\Widgets\FormWidget;

class FormFieldSwitchEstablish extends FormWidget
{

    public function handle()
    {
        $message = print_r(input(), true);
        return Resp::success($message);
    }

    /**
     */
    public function data(): array
    {
        return [
            'is_enable'     => '1',
            'is_enable_int' => 1,
            'is_text'       => 0,
            'is_enable-code'     => <<<CODE
\$this->onOff('is_enable', '是否开启');
CODE,
            'is_enable_int-code' => <<<CODE
\$this->onOff('is_enable_int', '是否开启:Int');
CODE,
            'is_text-code'       => <<<CODE
\$this->onOff('is_text', '开启')->text('开', '关');
CODE,
        ];
    }

    public function form()
    {
        $this->onOff('is_enable', '是否开启');
        $this->code('is_enable-code');
        $this->onOff('is_enable_int', '是否开启:Int');
        $this->code('is_enable_int-code');
        $this->onOff('is_text', '开启')->text('开', '关');
        $this->code('is_text-code');
    }
}
