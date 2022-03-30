<?php

namespace Poppy\MgrApp\Http\MgrApp;

use Poppy\Framework\Validation\Rule;
use Poppy\MgrApp\Classes\Form\SettingBase;
use Poppy\System\Action\Sso;

class SettingPam extends SettingBase
{

    protected string $title = 'Pam设置';

    protected string $group = 'py-system::pam';

    public function form()
    {
        $this->text('prefix', '账号前缀')->rules([
            Rule::required(),
        ])->placeholder('请输入账号前缀, 用于账号注册默认用户名生成');
        $this->onOff('auto_enable', '账号自动解封')->help('账号自动解封, 默认时间 15 分钟执行一次');
        $this->textarea('test_account', '测试账号')->placeholder('请填写测试账号, 每行一个')->help('在此测试账号内的应用, 不需要正确的验证码即可登录');

        $this->divider('单点登录设定');
        $this->radio('sso_type', '单点登录类型')->options(Sso::kvType())->rules([
            Rule::required(),
        ])->help('单点设备登录均会影响到线上用户, 请慎重处理. 设备组设定为 app(android/ios), web(h5/webapp/mp[小程序]), pc(mac/linux/win)');
        $this->text('sso_device_num', '最大设备数量')->help('启用多端登录时候允许的最大设备数量, 没有配置则默认最大数量为10')->rules([
            Rule::max(10), Rule::required(), Rule::numeric(),
        ]);
        $this->divider('账号验证码');
        $this->text('captcha_expired', '验证码有效期(分钟)')->rules([
            Rule::integer(),
        ])->default(5)->help('默认有效期 5 分钟');
        $this->text('captcha_length', '验证码长度')->help('验证码长度, 默认是 6 位, 可以设置的长度值是 4-10 位')->rules([
            Rule::between(4, 10), Rule::integer(),
        ])->default(6);
    }
}
