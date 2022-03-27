<?php

namespace Demo\Services;

use Poppy\Core\Services\Contracts\ServiceArray;
use Poppy\MgrPage\Http\MgrPage\FormSettingPam;

class SettingsKeyB implements ServiceArray
{

    public function key(): string
    {
        return 'demo.key-b';
    }

    public function data():array
    {
        return [
            'title' => 'KEY-B',
            'forms' => [
                FormSettingPam::class,
            ],
        ];
    }
}