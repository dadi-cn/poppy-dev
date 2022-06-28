<?php

namespace Demo\App\Forms;

use Poppy\Framework\Classes\Resp;
use Poppy\MgrApp\Classes\Widgets\FormWidget;

class FormFieldGroupEstablish extends FormWidget
{

    public function handle()
    {
        return Resp::success(var_export(input(), true));
    }

    /**
     */
    public function data(): array
    {
        return [
            'group' => [
                1   => '1-value',
                'a' => 'a-value',
            ],
        ];
    }

    public function form()
    {
        $this->text('group.1', '1Value');
        $this->text('group.a', 'Group Val');
    }
}
