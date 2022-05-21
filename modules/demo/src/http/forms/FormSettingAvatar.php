<?php


namespace Demo\Http\Forms;

use Poppy\Framework\Validation\Rule;
use Poppy\MgrPage\Classes\Form\FormSettingBase;

/**
 * 默认头像配置
 * Class FormSettingAvatar
 * @package Misc\Http\Forms\Settings
 */
class FormSettingAvatar extends FormSettingBase
{
    public $title = '用户默认头像';

    protected $group = 'misc::user';

    /**
     * Build a form here.
     */
    public function form()
    {
        $this->image('default_avatar', '用户默认头像')->rules([
            Rule::nullable(),
            Rule::required(),
            Rule::url(),
        ])->placeholder('默认头像');
    }
}