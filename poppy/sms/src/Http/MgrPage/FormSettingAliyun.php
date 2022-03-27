<?php

namespace Poppy\Sms\Http\MgrPage;

use Poppy\Framework\Validation\Rule;
use Poppy\MgrPage\Classes\Form\FormSettingBase;

class FormSettingAliyun extends FormSettingBase
{

    protected $title = '阿里云短信配置';

    protected $withContent = true;

    protected $group = 'py-sms::sms';

    /**
     * Build a form here.
     */
    public function form()
    {
        $this->text('aliyun_access_key', '阿里云 Key')->rules([
            Rule::nullable(),
        ]);
        $this->text('aliyun_access_secret', '阿里云 Secret')->rules([
            Rule::nullable(),
        ]);
    }
}
