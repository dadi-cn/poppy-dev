<?php

namespace Poppy\System\Classes\Contracts;

use Illuminate\Http\Request;

interface ApiSignContract
{
    /**
     * 获取Sign
     * @param array $params 参数
     * @return string
     */
    public function sign(array $params): string;


    /**
     * 检测签名
     * @param Request $request
     * @return bool
     */
    public function check(Request $request): bool;

    /**
     * 返回时间戳
     * @return int
     */
    public static function timestamp(): int;
}