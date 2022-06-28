<?php

namespace Poppy\MgrApp\Classes\Grid\Query;

use Closure;
use Illuminate\Support\Collection;
use Poppy\MgrApp\Classes\Filter\FilterPlugin;
use Poppy\MgrApp\Classes\Table\TablePlugin;

class QueryCustom extends Query
{

    /**
     * 表格
     * @var TablePlugin
     */
    protected TablePlugin $table;

    /**
     * 当前的页码
     * @var int
     */
    protected int $page;

    /**
     * 页码偏移量
     * @var int
     */
    protected int $pageOffset;

    /**
     * 查询条件, 包含Scope
     * @var array
     */
    protected array $params = [];

    /**
     * 实现 Get 方法来获取数据
     * @return Collection
     */
    public function get(): Collection
    {
        return new Collection();
    }

    /**
     * 用于批量查询
     * @param Closure $closure
     * @param int $amount
     * @return bool
     */
    public function chunk(Closure $closure, int $amount = 100)
    {
        return false;
    }

    /**
     * 用户筛选查询
     * @param FilterPlugin $filter
     * @param TablePlugin $table
     * @return $this
     */
    public function prepare(FilterPlugin $filter, TablePlugin $table): Query
    {
        $page             = (int) input(self::NAME_PAGE);
        $this->params     = $filter->prepare();
        $this->table      = $table;
        $this->page       = max($page, 1);
        $this->pageOffset = ($this->page - 1) * $this->pagesize;
        return $this;
    }

    /**
     * 编辑条目
     * @param $id
     * @param string $field
     * @param $value
     * @return bool
     */
    public function edit($id, string $field, $value): bool
    {
        return false;
    }

    /**
     * 导出 | 使用主键
     * @param array $ids
     * @return $this
     */
    public function usePrimaryKey(array $ids = []): Query
    {
        return $this;
    }

    /**
     * 获取主键
     * @return string
     */
    public function getPrimaryKey(): string
    {
        return '';
    }
}
