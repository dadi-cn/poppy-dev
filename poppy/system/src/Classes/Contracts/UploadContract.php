<?php

namespace Poppy\System\Classes\Contracts;

use Psr\Http\Message\StreamInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * 图片上传类
 */
interface UploadContract
{

    /**
     * 上传文件夹地址
     */
    public function __construct();

    /**
     * 设置返回地址
     * @param string $url 地址
     */
    public function setReturnUrl(string $url);

    /**
     * Set Extension
     * @param array $extension 支持的扩展
     */
    public function setExtension(array $extension = []);

    /**
     * 重新设置存储文件夹
     * @param $folder
     * @return mixed
     */
    public function setFolder($folder);

    /**
     * 类型
     * @param string $type
     * @return mixed
     */
    public function setType(string $type);

    /**
     * District Size.
     * @param int $resize 设置resize 的区域
     */
    public function setResizeDistrict(int $resize);

    /**
     * 设置图片压缩质量
     * @param int $quality
     */
    public function setQuality(int $quality);

    /**
     * 设置图片mime类型
     * @param $mime_type
     */
    public function setMimeType($mime_type);

    /**
     * 使用文件/form 表单形式上传获取并且保存
     * @param UploadedFile $file file 对象
     * @return mixed
     */
    public function saveFile(UploadedFile $file): bool;

    /**
     * 裁剪和压缩
     * @param mixed $content 需要压缩的内容
     * @param int   $width   宽度
     * @param int   $height  高度
     * @param bool  $crop    是否进行裁剪
     * @return StreamInterface
     */
    public function resize($content, $width = 1920, $height = 1440, $crop = false): StreamInterface;

    /**
     * 保存内容或者流方式上传
     * @param mixed $content 内容流
     * @return bool
     */
    public function saveInput($content): bool;

    /**
     * 获取目标路径
     * @return string
     */
    public function getDestination(): string;

    /**
     * @param string $destination 设置目标地址
     */
    public function setDestination(string $destination);

    /**
     * 图片url的地址
     * @return string
     */
    public function getUrl(): string;


    /**
     * 前缀Url
     * @return string
     */
    public function getReturnUrl(): string;


    /**
     * 启用水印
     * @return void
     */
    public function enableWatermark(): void;

    /**
     * 将上传成功的文件复制到另外一个位置
     * @param string $dist 目标路径
     * @return bool
     */
    public function copyTo(string $dist): bool;

    /**
     * 删除目标文件
     * @return bool
     */
    public function delete(): bool;
}