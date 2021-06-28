<?php

namespace Poppy\Version\Http\Forms\Backend;

use Poppy\Framework\Validation\Rule;
use Poppy\System\Classes\Traits\PamTrait;
use Poppy\System\Http\Forms\Settings\FormSettingBase;

class FormSettingVersion extends FormSettingBase
{
    use PamTrait;

    protected $withContent = true;

    protected $group = 'py-version::setting';

    public function data(): array
    {
        $data = parent::data();

        $data['path']        = $data['path'] ?: '/static/app';
        $data['latest_name'] = $data['latest_name'] ?: 'latest';
        return $data;
    }

    public function form()
    {
        $this->text('path', '路径')->rules([
            Rule::required(),
            Rule::regex('/^[a-z_0-9\/]{3,}$/'),
        ])->help('Apk/Ipa文件存放路径, 默认是 `static/app/`, 允许英文字母, 数字, 下划线, 目录分隔线');
        $this->text('latest_name', '最新版文件名称')->rules([
            Rule::regex('/^[a-z_0-9]{3,}$/'),
            Rule::required(),
        ])->help('最新版文件名称, 每次更新版本的时候选中更新最新文件则会自动覆盖此文件, 默认 `latest`, 仅允许英文字母数字, 下划线');
        $this->switch('ios_is_prod', '是否启用苹果官方链接')->help('启用官方链接后, 所有的IOS返回均为最新的线上链接');
        $this->text('ios_store_url', 'AppStore链接')->rules([
            Rule::nullable(),
            Rule::url(),
        ])->help('开启 IOS 更新后需要填写的链接地址');
    }
}
