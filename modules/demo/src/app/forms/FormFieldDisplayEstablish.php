<?php

namespace Demo\App\Forms;

use Poppy\Framework\Classes\Resp;
use Poppy\Framework\Validation\Rule;
use Poppy\MgrApp\Classes\Widgets\FormWidget;

class FormFieldDisplayEstablish extends FormWidget
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
            'radio'  => 'a',
            'chid'   => '',
            'mobile' => '',
        ];
    }

    public function form()
    {
        $this->radio('type', '单选')->options([
            'id'     => '身份证',
            'mobile' => '手机号',
        ]);
        $this->text('chid', '身份证')->rules([
            Rule::requiredIf('type', ['id']),
        ]);
        $this->text('mobile', '手机号')->rules([
            Rule::requiredIf('type', ['mobile']),
        ]);
    }
}
