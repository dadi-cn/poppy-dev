<?php

namespace Poppy\MgrPage\Classes\Grid;

use Closure;

interface ListContract
{
    /**
     * 添加列展示
     * @return mixed
     */
    public function columns();

    /**
     * 添加搜索项
     * @return Closure
     */
    public function filter(): Closure;

    /**
     * 添加操作项目
     * @return mixed
     */
    public function actions();

    /**
     * 批量操作
     * @return array
     */
    public function batchAction(): array;


    /**
     * 定义右上角的快捷操作栏
     * @return array
     */
    public function quickButtons(): array;
}
