<?php

namespace Poppy\Sms\Http\MgrPage;

use Poppy\Framework\Validation\Rule;
use Poppy\MgrPage\Classes\Form\FormSettingBase;

class FormSettingChuanglan extends FormSettingBase
{

    protected $title = '创蓝短信配置';

    protected $withContent = true;

    protected $group = 'py-sms::sms';

    /**
     * Build a form here.
     */
    public function form()
    {
        $this->text('chuanglan_access_key', '创蓝 Key')->rules([
            Rule::nullable(),
        ]);
        $this->text('chuanglan_access_secret', '创蓝 Secret')->rules([
            Rule::nullable(),
        ]);
        $this->text('chuanglan_cty_access_key', '创蓝国际 Key')->rules([
            Rule::nullable(),
        ]);
        $this->text('chuanglan_cty_access_secret', '创蓝国际 Secret')->rules([
            Rule::nullable(),
        ]);
    }
}
