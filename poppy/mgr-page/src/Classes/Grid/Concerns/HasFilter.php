<?php

namespace Poppy\MgrPage\Classes\Grid\Concerns;

use Closure;
use Illuminate\Contracts\View\Factory;
use Illuminate\Support\Collection;
use Illuminate\View\View;
use Poppy\MgrPage\Classes\Grid\Filter;
use Throwable;

/**
 * 是否开启筛选
 */
trait HasFilter
{
    /**
     * @var Filter
     */
    protected $filter;

    /**
     * 获取筛选
     *
     * @return Filter
     */
    public function getFilter(): Filter
    {
        return $this->filter;
    }

    /**
     * 执行查询器
     * @param bool $toArray
     * @return array|Collection|mixed
     */
    public function applyFilter($toArray = true)
    {
        return $this->filter->execute($toArray);
    }

    /**
     * Set the grid filter.
     * @param Closure $callback
     */
    public function filter(Closure $callback)
    {
        call_user_func($callback, $this->filter);
    }

    /**
     * Render the grid filter.
     *
     * @return Factory|View|string
     * @throws Throwable
     */
    public function renderFilter()
    {
        return $this->filter->render();
    }

    /**
     * 初始化筛选
     *
     * @return $this
     */
    protected function initFilter()
    {
        $this->filter = new Filter($this->model());

        return $this;
    }
}
