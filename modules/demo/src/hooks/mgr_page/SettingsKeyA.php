<?php

namespace Demo\Services\MgrPage;

use Poppy\Core\Services\Contracts\ServiceArray;
use Poppy\MgrPage\Http\MgrPage\FormSettingSite;

class SettingsKeyA implements ServiceArray
{

    public function key(): string
    {
        return 'demo.key-a';
    }

    public function data():array
    {
        return [
            'title' => 'KEY-A',
            'group' => 'key-a',
            'forms' => [
                FormSettingSite::class,
            ],
        ];
    }
}