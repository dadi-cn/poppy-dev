<?php

namespace Demo\App\Forms;

use Poppy\Framework\Classes\Resp;
use Poppy\Framework\Validation\Rule;
use Poppy\MgrApp\Classes\Widgets\FormWidget;

class FormFieldRadioEstablish extends FormWidget
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
            'id'    => 5,
            'radio' => 'a',

            'radio-code'   => <<<CODE
\$this->radio('radio', '单选')->options([
    'a' => 'A',
    'b' => 'B',
]);
CODE,
            'button-code'  => <<<CODE
\$this->radio('button', '默认:b')->options([
    'a' => 'A',
    'b' => 'B',
])->default('b')->button();
CODE,
            'complex-code' => <<<CODE
\$this->radio('complex', '复杂组合(禁用某一个条目)')->options([
    ['value' => 'a', 'disabled' => false, 'label' => 'Label A'],
    ['value' => 'b', 'disabled' => true, 'label' => 'Label B'],
])->default('a')->button();
CODE,
        ];
    }

    public function form()
    {
        $this->radio('radio', '单选')->options([
            'a' => 'A',
            'b' => 'B',
        ]);
        $this->code('radio-code');
        $this->radio('button', '默认:b')->options([
            'a' => 'A',
            'b' => 'B',
        ])->default('b')->button();
        $this->code('button-code');
        $this->radio('complex', '复杂组合(禁用某一个条目)')->options([
            ['value' => 'a', 'disabled' => false, 'label' => 'Label A'],
            ['value' => 'b', 'disabled' => true, 'label' => 'Label B'],
        ])->default('a')->button();
        $this->code('complex-code');
        $this->text('radio-a', 'Radio-A')->rules([
           Rule::requiredIf('radio', ['a'])
        ]);
        $this->text('radio-b', 'Radio-B')->rules([
           Rule::requiredIf('radio', ['b'])
        ]);
    }
}
