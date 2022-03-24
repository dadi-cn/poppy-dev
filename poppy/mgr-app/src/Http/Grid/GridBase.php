<?php

namespace Poppy\MgrApp\Http\Grid;

use Poppy\MgrApp\Classes\Grid\Tools\Actions;
use Poppy\MgrApp\Classes\Widgets\FilterWidget;
use Poppy\MgrApp\Classes\Widgets\TableWidget;
use Poppy\System\Models\PamAccount;

/**
 * @property-read string $title    标题
 */
abstract class GridBase
{
    /**
     * 标题
     * @var string
     */
    protected string $title = '';


    /**
     * @var PamAccount
     */
    protected $pam;

    public function __construct()
    {
        $this->pam = app('auth')->user();
    }


    /**
     * 表格定义
     * @param TableWidget $table
     * @return void
     */
    public function table(TableWidget $table)
    {
    }

    /**
     * 搜索项
     * @param FilterWidget $filter
     * @return void
     */
    public function filter(FilterWidget $filter)
    {
    }

    /**
     * 快捷操作栏
     * @param Actions $actions
     * @return void
     */
    public function quick(Actions $actions)
    {
    }

    /**
     * 批量操作
     * @param Actions $actions
     * @return void
     */
    public function batch(Actions $actions)
    {
    }

    public function __get($attr)
    {
        return $this->{$attr} ?? '';
    }
}
