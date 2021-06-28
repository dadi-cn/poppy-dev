<?php

namespace Poppy\System\Classes\Uploader;

use Poppy\System\Classes\Contracts\UploadContract;

/**
 * 图片上传类
 */
class Uploader
{

    public const TYPE_IMAGES = 'images';
    public const TYPE_FILE   = 'file';
    public const TYPE_VIDEO  = 'video';
    public const TYPE_AUDIO  = 'audio';

    /**
     * 获取可用扩展
     * @param string $type
     * @return array
     */
    public static function kvExt(string $type): array
    {
        $desc = [
            self::TYPE_IMAGES => ['jpg', 'jpeg', 'png', 'gif', 'bmp', 'svg'],
            self::TYPE_FILE   => [
                'html', 'htm',
                'doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx', 'pps', 'potx', 'pot',
                'rtf', 'odt', 'pages', 'ai', 'dxf', 'ttf', 'tiff', 'tif', 'wmf', 'eps',
                'txt', 'md', 'csv', 'nfo', 'ini', 'json', 'js', 'css', 'ts', 'sql',
                'zip', 'rp', 'rplib', 'svga',
                'pdf',
                'apk', 'ipa',
            ],
            self::TYPE_VIDEO  => ['mp4', 'rm', 'rmvb', 'wmv', 'webm', 'mpg', 'mov', '3gp'],
            self::TYPE_AUDIO  => ['mp3', 'm4a', 'wav', 'aac'],
        ];
        return kv($desc, $type);
    }

    /**
     * 获取描述
     * @param string $type
     * @return string
     */
    public static function kvDesc(string $type): string
    {
        $desc = [
            self::TYPE_IMAGES => '请选择图片',
            self::TYPE_AUDIO  => '请选择音频文件',
            self::TYPE_VIDEO  => '请选择视频文件',
            self::TYPE_FILE   => '选择文件',
        ];
        return kv($desc, $type);
    }


    /**
     * 上传的前缀地址
     * @return string
     */
    public static function prefix():string
    {
        return app(UploadContract::class)->getReturnUrl();
    }
}