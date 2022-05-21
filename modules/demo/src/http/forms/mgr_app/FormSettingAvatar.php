<?php

namespace Demo\Http\Forms\MgrApp;

use Poppy\Framework\Validation\Rule, Poppy\MgrApp\Classes\Form\SettingBase;
use Poppy\MgrPage\Classes\Form\FormSettingBase;
/**
 * 默认头像配置
 * Class FormSettingAvatar
 * @package Misc\Http\Forms\Settings
 */
class FormSettingAvatar extends SettingBase
{
    protected string $title = '用户默认头像';
    protected string $group = 'misc::user';
    /**
     * Build a form here.
     */
    public function form()
    {
        $this->image('default_avatar', '用户默认头像')->rules([Rule::nullable(), Rule::required(), Rule::url()]);
    }
}