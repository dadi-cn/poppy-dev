<?php

namespace Demo\Forms;

use Poppy\Framework\Validation\Rule;

class FormTextArea extends FormBaseWidget
{
    /**
     * 表单标题
     * @var string
     */
    protected $title = 'TextArea';


    /**
     * Build a form here.
     */
    public function form()
    {
        $this->textarea('content', 'Content')->rows(10)->help('文本行默认显示10行');
        // 添加 code 代码
        $code = <<<CODE
\$this->textarea('content', 'Content')->rows(10)->help('文本行默认显示10行');
CODE;
        $this->code('content-code', 'Code@Content')->default($code);
        $this->divider();

        $this->textarea('content_required', 'Content Required')->rules([
            Rule::required(),
        ])->help('文本内容必须填写');
        // 添加 code 代码
        $code = <<<CODE
\$this->textarea('content_required', 'Content Required')->rules([
    Rule::required(),
])->help('文本内容必须填写');
CODE;
        $this->code('content_required-code', 'Code@Content Required')->default($code);
    }
}