<?php

namespace Poppy\AliyunOss\Hooks\System;

use Poppy\AliyunOss\Classes\Provider\OssDefaultUploadProvider;
use Poppy\AliyunOss\Http\MgrPage\FormSettingAliyunOss;
use Poppy\Core\Services\Contracts\ServiceArray;

class UploadTypeAliyun implements ServiceArray
{

    public function key(): string
    {
        return 'aliyun';
    }

    public function data(): array
    {
        return [
            'title'    => '阿里云存储(Oss)',
            'provider' => OssDefaultUploadProvider::class,
            'setting'  => FormSettingAliyunOss::class,
            'path'     => 'form/py-aliyun-oss:api-backend.home.store',
            'route'    => 'py-aliyun-oss:backend.upload.store',
        ];
    }
}