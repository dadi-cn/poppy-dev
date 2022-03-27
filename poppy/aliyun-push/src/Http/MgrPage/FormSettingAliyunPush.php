<?php

declare(strict_types = 1);

namespace Poppy\AliyunPush\Http\MgrPage;

use Poppy\Framework\Validation\Rule;
use Poppy\MgrPage\Classes\Form\FormSettingBase;

class FormSettingAliyunPush extends FormSettingBase
{

    protected $title = '推送配置';

    protected $group = 'py-aliyun-push::push';

    /**
     * Build a form here.
     */
    public function form()
    {
        $this->text('access_key', 'AccessKey(阿里云)')->rules([
            Rule::nullable(),
        ]);
        $this->text('access_secret', 'AccessSecret(阿里云)')->rules([
            Rule::nullable(),
        ]);
        $this->switch('android_is_open', '是否开启 Android 推送');
        $this->text('android_app_key', 'Android AppKey')->rules([
            Rule::nullable(),
        ]);
        $this->text('android_channel', 'Android 通道')->help('Android 8.0 之后需要');
        $this->text('android_activity', 'Android Activity')->help('Android Activity');
        $this->switch('ios_is_open', '是否开启 iOS 推送');
        $this->text('ios_app_key', 'iOS AppKey')->rules([
            Rule::nullable(),
        ]);
    }
}
