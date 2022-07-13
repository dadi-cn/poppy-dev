<?php

declare(strict_types = 1);

namespace Poppy\Version\Classes;

class PyVersionDef
{
    /**
     * 当前最大版本号缓存
     * @return string
     */
    public static function ckTagMaxVersion(): string
    {
        return 'tag:py-version:max-version';
    }


    /**
     * 当前所有版本
     * @return string
     */
    public static function ckTagVersions(): string
    {
        return 'tag:py-version:versions';
    }
}