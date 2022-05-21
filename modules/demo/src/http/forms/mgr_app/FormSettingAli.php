<?php

namespace Demo\Http\Forms\MgrApp;

use Poppy\Framework\Validation\Rule, Poppy\MgrApp\Classes\Form\SettingBase;
use Poppy\MgrPage\Classes\Form\FormSettingBase;
/**
 * 支付宝-支付设置
 */
class FormSettingAli extends SettingBase
{
    protected string $title = '支付宝设置';
    protected string $group = 'misc::site';
    /**
     * Build a form here.
     */
    public function form() : void
    {
        $this->radio('alipay_app_open', 'APP-支付开关')->options(['0' => '关闭', '1' => '开启']);
        $this->radio('alipay_h5_open', 'H5-支付开关')->options(['0' => '关闭', '1' => '开启']);
        $this->radio('alipay_pc_open', 'PC-支付开关')->options(['0' => '关闭', '1' => '开启']);
        $this->radio('open_to_account', '企业支付宝提现到个人账户')->options(['0' => '关闭', '1' => '开启']);
        $this->radio('alipay_is_sandbox', '沙箱')->options(['0' => '关闭', '1' => '开启']);
        $this->text('alipay_app_id', 'APP-应用ID')->rules([Rule::required(), Rule::nullable(), Rule::string()]);
        $this->textarea('alipay_private_key', 'APP-私钥')->rules([Rule::required(), Rule::nullable(), Rule::string()]);
        $this->textarea('alipay_public_key', 'APP-公钥')->rules([Rule::nullable(), Rule::string()]);
        $this->text('mapi_partner', 'mApi-Partner')->rules([Rule::required(), Rule::nullable(), Rule::string()]);
        $this->textarea('mapi_private_key', 'mApi-私钥')->rules([Rule::required(), Rule::nullable(), Rule::string()]);
        $this->text('alipay_mini_app_id', '小程序-应用ID')->rules([Rule::required(), Rule::nullable(), Rule::string()]);
        $this->textarea('alipay_mini_private_key', '小程序-私钥')->rules([Rule::required(), Rule::nullable(), Rule::string()]);
        $this->textarea('alipay_mini_public_key', '小程序-公钥')->rules([Rule::required(), Rule::nullable(), Rule::string()]);
    }
}