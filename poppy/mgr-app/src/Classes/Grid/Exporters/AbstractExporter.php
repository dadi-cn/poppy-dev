<?php

namespace Poppy\MgrApp\Classes\Grid\Exporters;

use Closure;
use Exception;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Poppy\MgrApp\Classes\Contracts\Exportable;
use Poppy\MgrApp\Classes\Contracts\Query;
use Poppy\MgrApp\Classes\Filter\FilterPlugin;
use Poppy\MgrApp\Classes\Grid\Exporter;
use Poppy\MgrApp\Classes\Table\TablePlugin;

/**
 * Exporter 类
 */
abstract class AbstractExporter implements Exportable
{
    /**
     * @var int
     */
    protected int $page = 1;

    /**
     * 文件名称
     * @var string
     */
    protected string $fileName = '';


    protected string $title = '';


    protected Query $query;

    protected FilterPlugin $filter;

    protected TablePlugin $table;

    /**
     * 扩展新实例
     */
    public function __construct(Query $model, FilterPlugin $filterWidget, TablePlugin $columnWidget, $title)
    {
        $this->query  = $model;
        $this->filter = $filterWidget;
        $this->table  = $columnWidget;
        $this->title  = $title;
    }

    /**
     * 数据分块
     * @param Closure $callback
     * @param int $count
     * @return bool|Collection
     * @throws Exception
     */
    public function chunk(Closure $callback, int $count = 100)
    {
        return $this->query->prepare($this->filter, $this->table)->chunk($callback, $count);
    }


    /**
     * 为模型设置查询配置
     * @param string $scope
     * @return $this
     */
    public function withScope(string $scope = 'page'): self
    {
        if ($scope == Exporter::SCOPE_ALL || $scope === Exporter::SCOPE_QUERY) {
            $this->query->usePaginate(false);
            $this->fileName = $this->title() . '-' . ($scope === 'all' ? '全部' : '查询结果');
            return $this;
        }

        if ($scope == Exporter::SCOPE_PAGE) {
            $this->query->usePaginate(true);
            $this->page     = input('page', 1);
            $this->fileName = $this->title() . "-第{$this->page}页";
        }

        if ($scope == Exporter::SCOPE_SELECT) {
            $selected = input(TablePlugin::NAME_BATCH);
            if (is_string($selected)) {
                $selected = explode(',', $selected);
            }
            $count          = count($selected);
            $this->fileName = $this->title() . "-已选择({$count})";
            $this->query->usePrimaryKey($selected);
        }
        return $this;
    }

    /**
     * @inheritDoc
     */
    abstract public function export();

    private function title(): string
    {
        if (!$this->title) {
            return Carbon::now()->format('Y-m-d H:i');
        } else {
            return $this->title;
        }
    }
}
