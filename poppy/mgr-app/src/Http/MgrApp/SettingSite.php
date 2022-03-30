<?php

namespace Poppy\MgrApp\Http\MgrApp;

use Poppy\Framework\Validation\Rule;
use Poppy\MgrApp\Classes\Form\SettingBase;

class SettingSite extends SettingBase
{
    protected string $title = '站点设置';

    protected string $group = 'py-system::site';

    /**
     * Build a form here.
     */
    public function form()
    {
        $this->text('name', '网站名称')->rules([
            Rule::required(),
        ])->placeholder('请输入网站名称')->default('罂粟网络');
        $this->text('title', '网站标题')->rules([
            Rule::nullable(),
        ])->placeholder('请输入网站标题, 将显示在标题栏中');
        $this->text('keywords', '网站关键词')->rules([
            Rule::nullable(),
        ]);
        $this->textarea('description', '网站描述')->placeholder('请输入网站描述');
        $this->image('logo', 'Logo')->rules([
            Rule::nullable(),
        ])->help('后台Logo 在此更换, 更改左上角的Logo 地址');
        $this->onOff('is_open', '站点开启')->rules([
            Rule::boolean(),
        ])->default(1)->help('关闭后网站将不能访问');
        $this->text('statistics_code', '统计代码');
        $this->textarea('close_reason', '网站停止服务说明')->placeholder('网站暂停服务描述')->help('站点关闭原因');
    }
}
