<?php

namespace Demo\App\Forms;

use Poppy\Framework\Classes\Resp;
use Poppy\Framework\Exceptions\FakerException;
use Poppy\Framework\Validation\Rule;
use Poppy\MgrApp\Classes\Widgets\FormWidget;

class FormFieldTextEstablish extends FormWidget
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
            'id'               => 5,
            'default'          => 'default str',
            'default-code'     => <<<HTML
\$this->text('default', '文本');
HTML,
            'disabled-code'    => <<<HTML
\$this->text('disabled', '禁用')->disabled();
HTML,
            'placeholder-code' => <<<HTML
\$this->text('placeholder', '占位符')->placeholder('占位符');
HTML,
            'clearable-code'   => <<<HTML
\$this->text('clearable', 'Clearable')->clearable();
HTML,
            'prefix-icon-code' => <<<HTML
\$this->text('prefix-icon', '带有Icon(头部)')->prefixIcon('Search');
HTML,
            'suffix-icon-code' => <<<HTML
\$this->text('suffix-icon', '带有Icon(尾部)')->suffixIcon('Calendar');
HTML,
            'divider-code'     => <<<HTML
\$this->divider('divider', '分割线');
HTML,
            'max-length-code'  => <<<HTML
\$this->text('max-length', '最大长度')->rules([
    Rule::max(20),
])->showWordLimit();
HTML,
            'url-code'         => <<<HTML
\$this->url('url', 'Url')->rules([
    Rule::max(20),
]);
HTML,
            'password-code'    => <<<HTML
\$this->password('password', 'Password');
HTML,
            'mobile-code'      => <<<HTML
\$this->mobile('mobile', 'Mobile');
HTML,
            'ip-code'          => <<<HTML
\$this->ip('ip', 'Ip');
HTML,
            'decimal-code'     => <<<HTML
\$this->decimal('decimal', 'Decimal(3位小数)')->rules([
    Rule::between(0.000, 1.000),
])->digits(3);
HTML,
            'email-code'       => <<<HTML
\$this->email('email', '邮箱');
HTML,
            'currency-code'    => <<<HTML
\$this->currency('currency', '人民币');
HTML,
            'monospace-code'   => <<<HTML
\$this->text('monospace', '等宽字体')->monospace()
HTML,
            'monospace'        => <<<HTML
\$this->currency('currency', '人民币');
HTML,
            'disabled'         => py_faker()->text(20),
        ];
    }

    public function form()
    {
        $this->text('default', '文本');
        $this->code('default-code');
        $this->text('disabled', '禁用')->disabled()->help('当前组件是禁用状态');
        $this->code('disabled-code');
        $this->text('placeholder', '占位符')->placeholder('占位符');
        $this->code('placeholder-code');
        $this->text('clearable', 'Clearable')->clearable();
        $this->code('clearable-code');
        $this->text('prefix-icon', '带有Icon(头部)')->prefixIcon('Search');
        $this->code('prefix-icon-code');
        $this->text('suffix-icon', '带有Icon(尾部)')->suffixIcon('Calendar');
        $this->code('suffix-icon-code');
        $this->divider('divider', '分割线');
        $this->code('divider-code');
        $this->text('max-length', '最大长度')->rules([
            Rule::max(20),
        ])->showWordLimit();
        $this->code('max-length-code');
        $this->url('url', 'Url')->rules([
            Rule::max(20),
        ]);
        $this->code('url-code');
        $this->password('password', 'Password');
        $this->code('password-code');
        $this->mobile('mobile', 'Mobile');
        $this->code('mobile-code');
        $this->ip('ip', 'Ip');
        $this->code('ip-code');
        $this->decimal('decimal', 'Decimal(3位小数)')->rules([
            Rule::between(0.000, 1.000),
        ])->digits(3);
        $this->code('decimal-code');
        $this->email('email', '邮箱');
        $this->code('email-code');
        $this->currency('currency', '人民币');
        $this->code('currency-code');
        $this->text('monospace', '等宽字体')->monospace();
        $this->code('monospace-code');
    }
}
