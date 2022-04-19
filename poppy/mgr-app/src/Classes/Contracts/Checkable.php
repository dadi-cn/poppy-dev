<?php

namespace Poppy\MgrApp\Classes\Contracts;

interface Checkable
{
    /**
     * 返回错误数组, 如果没有错误返回空数组
     */
    public function check():array;
}
