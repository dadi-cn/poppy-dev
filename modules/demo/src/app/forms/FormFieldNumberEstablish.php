<?php

namespace Demo\App\Forms;

use Poppy\Framework\Classes\Resp;
use Poppy\Framework\Validation\Rule;
use Poppy\MgrApp\Classes\Widgets\FormWidget;
use Poppy\MgrApp\Exceptions\InvalidFieldParamException;

class FormFieldNumberEstablish extends FormWidget
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
            'id'        => 5,
            'default'   => 28,
            'step'      => 4,
            'range'     => 16,
            'disabled'  => 18,
            'precision' => 16,

            'default-code'   => <<<CODE
\$this->number('default', '默认');
CODE,
            'step-code'      => <<<CODE
\$this->number('step', '步进:2')->step(2);
CODE,
            'disabled-code'  => <<<CODE
\$this->number('disabled', '禁用')->disabled();
CODE,
            'range-code'     => <<<CODE
\$this->number('range', '4-28')->rules([
    Rule::max(28),
    Rule::min(4),
])->step(4);
CODE,
            'precision-code' => <<<CODE
\$this->number('precision', '精度:2')->rules([
    Rule::max(28),
    Rule::min(4),
])->step(0.08, true)->precision(2);
CODE,
        ];
    }

    /**
     * @throws InvalidFieldParamException
     */
    public function form()
    {
        $this->number('default', '默认');
        $this->code('default-code');
        $this->number('step', '步进:2')->step(2);
        $this->code('step-code');
        $this->number('disabled', '禁用')->disabled();
        $this->code('disabled-code');
        $this->number('range', '4-28')->rules([
            Rule::max(28),
            Rule::min(4),
        ])->step(4);
        $this->code('range-code');
        $this->number('precision', '精度:2')->rules([
            Rule::max(28),
            Rule::min(4),
        ])->step(0.08, true)->precision(2);
        $this->code('precision-code');
    }
}
