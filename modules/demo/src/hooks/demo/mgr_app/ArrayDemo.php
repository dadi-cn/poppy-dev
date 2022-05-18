<?php

namespace Demo\Hooks\Demo\MgrApp;

use Demo\Http\Forms\MgrApp\FormSettingAli;
use Demo\Http\Forms\MgrApp\FormSettingAvatar;
use Poppy\Core\Services\Contracts\ServiceArray;
/**
 * 选择广告位
 */
class ArrayDemo implements ServiceArray
{
    public function key() : string
    {
        return 'poppy-core-array-service';
    }
    public function data() : array
    {
        return ['title' => 'Demo', 'forms' => [FormSettingAli::class, FormSettingAvatar::class]];
    }
}