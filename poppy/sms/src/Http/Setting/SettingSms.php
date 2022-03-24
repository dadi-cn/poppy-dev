<?php

namespace Poppy\Sms\Http\Setting;

use Poppy\Framework\Validation\Rule;
use Poppy\MgrApp\Http\Setting\SettingBase;

class SettingSms extends SettingBase
{

    protected string $title = '短信配置';

    protected string $group = 'py-sms::sms';


    /**
     */
    public function form()
    {
        $sendTypes = sys_hook('poppy.sms.send_type');

        $types['local'] = '本地';
        foreach ($sendTypes as $key => $desc) {
            $types[$key] = $desc['title'];
        }

        $this->radio('send_type', '发送方式')->options($types)->rules([
            Rule::string(),
            Rule::required(),
        ])->default('local')->help('选择本地则文件存储在日志中, 需要自行查看');
        $this->text('sign', '默认签名')->rules([
            Rule::nullable(),
        ]);

        $this->divider('阿里云');
        $this->text('aliyun_access_key', '阿里云 Key')->rules([
            Rule::nullable(),
        ]);
        $this->text('aliyun_access_secret', '阿里云 Secret')->rules([
            Rule::nullable(),
        ]);

        $this->divider('创蓝');
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
