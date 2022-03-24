<?php

namespace Demo\App\Forms;

use Poppy\Framework\Classes\Resp;
use Poppy\Framework\Exceptions\FakerException;
use Poppy\Framework\Validation\Rule;
use Poppy\MgrApp\Classes\Widgets\FormWidget;

class FormFieldTextareaEstablish extends FormWidget
{

    public function handle()
    {
        return Resp::success('');
    }

    /**
     * @throws FakerException
     */
    public function data(): array
    {
        return [
            'id'       => 5,
            'default'  => 'default str',
            'disabled' => py_faker()->text(20),

            'default-code'         => <<<CODE
\$this->textarea('default', '文本区域');
CODE,
            'rows-code'            => <<<CODE
\$this->textarea('rows', '行高度:3')->rows(3);
CODE,
            'disabled-code'        => <<<CODE
\$this->textarea('disabled', '禁用')->disabled();
CODE,
            'placeholder-code'     => <<<CODE
\$this->textarea('placeholder', '占位符')->placeholder('占位符');
CODE,
            'autosize-code'        => <<<CODE
\$this->textarea('autosize', '自动大小')->autosize();
CODE,
            'autosize-2-8-code'    => <<<CODE
\$this->textarea('autosize-2-8', '自动大小:2-8')->autosize([
    'minRows' => 2, 'maxRows' => 8,
]);
CODE,
            'autosize-1-4-code'    => <<<CODE
\$this->textarea('autosize-1-4', '自动大小:1,4')->autosize(1, 4);
CODE,
            'show-word-limit-code' => <<<CODE
\$this->textarea('show-word-limit', '长度限制')->rules([
    Rule::max(20)
])->showWordLimit();
CODE,
            'resize-code'          => <<<CODE
\$this->textarea('resize', '长度限制')->resize('none');
CODE,
        ];
    }

    public function form()
    {
        $this->textarea('default', '文本区域');
        $this->code('default-code');
        $this->textarea('rows', '行高度:3')->rows(3);
        $this->code('rows-code');
        $this->textarea('disabled', '禁用')->disabled();
        $this->code('disabled-code');
        $this->textarea('placeholder', '占位符')->placeholder('占位符');
        $this->code('placeholder-code');
        $this->textarea('autosize', '自动大小')->autosize();
        $this->code('autosize-code');
        $this->textarea('autosize-2-8', '自动大小:2-8')->autosize([
            'minRows' => 2, 'maxRows' => 8,
        ]);
        $this->code('autosize-2-8-code');
        $this->textarea('autosize-1-4', '自动大小:1,4')->autosize(1, 4);
        $this->code('autosize-1-4-code');
        $this->textarea('show-word-limit', '长度限制')->rules([
            Rule::max(20)
        ])->showWordLimit();
        $this->code('show-word-limit-code');
        $this->textarea('resize', '长度限制')->resize('none');
        $this->code('resize-code');
    }
}
