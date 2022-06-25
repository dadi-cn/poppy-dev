<?php

namespace Poppy\MgrApp\Classes\Grid\Query;

use Closure;
use Poppy\MgrApp\Classes\Contracts\Queryable as QueryContract;

/**
 * @property integer $usePaginate 是否使用分页
 * @property integer $pagesize 分页数量
 */
abstract class Query implements QueryContract
{

    protected const NAME_PAGESIZE = 'pagesize';       // 页数
    protected const NAME_PAGE     = 'page';           // 页码
    protected const OBJECT_MASK   = '--wb--';

    /*
     * 15 items per page as default.
     * @var int
     */
    protected int $pagesize = 15;

    /**
     * 使用分页
     * @var bool
     */
    protected bool $usePaginate = true;

    /**
     * 分页总长度
     * @var int
     */
    protected int $total = 0;


    /**
     * 对查询出来的数据集合进行回调, 参数是查询的所有数据
     * @var ?Closure
     */
    protected ?Closure $collectionCallback = null;

    /**
     * 启用或者禁用分页
     * @param bool $paginate
     * @return Query
     */
    public function usePaginate(bool $paginate = true): self
    {
        $this->usePaginate = $paginate;
        return $this;
    }


    /**
     * 获取当前模型的总数
     * @return int
     */
    public function total(): int
    {
        return $this->total;
    }

    /**
     * Set collection callback.
     *
     * @param Closure|null $callback
     *
     * @return $this
     */
    public function collection(Closure $callback = null): self
    {
        $this->collectionCallback = $callback;
        return $this;
    }
}
