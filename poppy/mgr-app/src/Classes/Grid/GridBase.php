<?php

namespace Poppy\MgrApp\Classes\Grid;

use Poppy\MgrApp\Classes\Filter\FilterPlugin;
use Poppy\MgrApp\Classes\Grid\Tools\Actions;
use Poppy\MgrApp\Classes\Table\TablePlugin;
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
     * @param TablePlugin $table
     * @return void
     */
    public function table(TablePlugin $table)
    {
    }

    /**
     * 搜索项
     * @param FilterPlugin $filter
     * @return void
     */
    public function filter(FilterPlugin $filter)
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
