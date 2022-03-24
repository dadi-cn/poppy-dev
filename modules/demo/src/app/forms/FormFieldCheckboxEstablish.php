<?php

namespace Demo\App\Forms;

use Poppy\Framework\Classes\Resp;
use Poppy\Framework\Validation\Rule;
use Poppy\MgrApp\Classes\Widgets\FormWidget;

class FormFieldCheckboxEstablish extends FormWidget
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
            'id'       => 5,
            'checkbox' => ['a'],
            'checkbox-code' => <<<CODE
\$this->checkbox('checkbox', '多选')->options([
    'a' => 'A',
    'b' => 'B',
]);
CODE,
            'check-all-code' => <<<CODE
\$this->checkbox('checkbox', '多选')->options([
    'a' => 'A',
    'b' => 'B',
]);
CODE,
            'check-all-btn-code' => <<<CODE
\$this->checkbox('check-all-btn', '多选(支持快捷选择)')->options([
    'a' => 'A',
    'b' => 'B',
])->checkAll()->button();
CODE,
            'button-code' => <<<CODE
\$this->checkbox('button', '默认:b')->options([
    'a' => 'A',
    'b' => 'B',
])->default('b')->button();
CODE,
            'complex-code' => <<<CODE
\$this->checkbox('complex', '复杂组合(禁用某一个条目)')->options([
    ['value' => 'a', 'disabled' => false, 'label' => 'Label A'],
    ['value' => 'b', 'disabled' => true, 'label' => 'Label B'],
])->default('a')->button();
CODE,
            'range-code' => <<<CODE
\$this->checkbox('range', '选择项在 2-8 之间')->options(array_combine(range('a', 'n'), range('A', 'N')))
    ->rules([
        Rule::min(2), Rule::max(8),
    ])
    ->default('a');
CODE,

        ];
    }

    public function form()
    {
        $this->checkbox('checkbox', '多选')->options([
            'a' => 'A',
            'b' => 'B',
        ]);
        $this->code('checkbox-code');
        $this->checkbox('check-all', '多选(支持快捷选择)')->options([
            'a' => 'A',
            'b' => 'B',
        ])->checkAll();
        $this->code('check-all-code');
        $this->checkbox('check-all-btn', '多选(支持快捷选择)')->options([
            'a' => 'A',
            'b' => 'B',
        ])->checkAll()->button();
        $this->code('check-all-btn-code');
        $this->checkbox('button', '默认:b')->options([
            'a' => 'A',
            'b' => 'B',
        ])->default('b')->button();
        $this->code('button-code');
        $this->checkbox('complex', '复杂组合(禁用某一个条目)')->options([
            ['value' => 'a', 'disabled' => false, 'label' => 'Label A'],
            ['value' => 'b', 'disabled' => true, 'label' => 'Label B'],
        ])->default('a')->button();
        $this->code('complex-code');
        $this->checkbox('range', '选择项在 2-8 之间')->options(array_combine(range('a', 'n'), range('A', 'N')))
            ->rules([
                Rule::min(2), Rule::max(8),
            ])
            ->default('a');
        $this->code('range-code');

    }
}
