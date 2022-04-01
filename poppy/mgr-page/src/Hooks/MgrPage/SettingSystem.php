<?php

namespace Poppy\MgrPage\Hooks\MgrPage;

use Poppy\Core\Services\Contracts\ServiceArray;
use Poppy\MgrPage\Http\MgrPage\FormSettingPam;
use Poppy\MgrPage\Http\MgrPage\FormSettingSite;

class SettingSystem implements ServiceArray
{

    public function key(): string
    {
        return 'poppy.mgr-page';
    }

    public function data(): array
    {
        return [
            'title' => '系统',
            'forms' => [
                FormSettingSite::class,
                FormSettingPam::class,
            ],
        ];
    }
}