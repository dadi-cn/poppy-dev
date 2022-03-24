<?php

namespace Poppy\MgrApp\Classes\Grid\Query;

use Closure;
use Illuminate\Support\Collection;
use Poppy\MgrApp\Classes\Grid\Filter\Query\Scope;
use Poppy\MgrApp\Classes\Widgets\FilterWidget;
use Poppy\MgrApp\Classes\Widgets\TableWidget;

class QueryCustom extends Query
{

    /**
     * 全局范围
     * @var Scope|null
     */
    protected ?Scope $scope;

    /**
     * 过滤器
     * @var FilterWidget
     */
    protected FilterWidget $filter;

    /**
     * 表格
     * @var TableWidget
     */
    protected TableWidget $table;

    /**
     * @return Collection
     */
    public function get(): Collection
    {
        return new Collection();
    }

    /**
     * 对于返回的列表数据进行回调调用
     * @param Closure|null $callback
     * @return $this
     */
    public function collection(Closure $callback = null): Query
    {
        return $this;
    }

    /**
     * 用于批量查询
     * @param Closure $closure
     * @param int $amount
     * @return mixed|bool
     */
    public function chunk(Closure $closure, int $amount = 100)
    {
        return false;
    }

    /**
     * 查询所有数据
     * @return int
     */
    public function total(): int
    {
        return 0;
    }

    /**
     * 用户筛选查询
     * @param FilterWidget $filter
     * @param TableWidget $table
     * @return $this
     */
    public function prepare(FilterWidget $filter, TableWidget $table): Query
    {
        $this->filter = $filter;
        $this->table  = $table;
        $this->scope  = $filter->getCurrentScope();
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
     * 使用分页
     * @param bool $paginate
     * @return mixed
     */
    public function usePaginate(bool $paginate = false): Query
    {
        return $this;
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
