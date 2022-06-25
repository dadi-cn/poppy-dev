<?php

namespace Poppy\MgrApp\Classes\Grid;

use Closure;
use Illuminate\Support\Collection;
use Poppy\Framework\Classes\Traits\PoppyTrait;
use Poppy\MgrApp\Classes\Filter\FilterPlugin;
use Poppy\MgrApp\Classes\Grid\Tools\Interactions;
use Poppy\MgrApp\Classes\Table\Column\Column;
use Poppy\MgrApp\Classes\Table\TablePlugin;
use Poppy\MgrApp\Classes\Traits\UseQuery;
use function collect;

/**
 * todo 可能需要和 widget 再进行合并
 * @property-read string $title 标题
 */
class GridPlugin
{
    use PoppyTrait;
    use UseQuery;

    /**
     * @var FilterPlugin
     */
    protected FilterPlugin $filter;


    /**
     * 列组件
     * @var TablePlugin
     */
    protected TablePlugin $table;

    /**
     * 右上角快捷操作
     * @var Interactions
     */
    protected Interactions $quick;

    /**
     * 左下角快捷操作
     * @var Interactions
     */
    protected Interactions $batch;

    /**
     * 标题
     * @var string
     */
    protected string $title = '';

    /**
     * Create a new grid instance.
     */
    public function __construct()
    {
        $this->filter = new FilterPlugin();
        $this->quick  = (new Interactions())->default(['plain', 'primary']);
        $this->batch  = (new Interactions())->default(['info', 'plain']);
        $this->table  = new TablePlugin();
    }

    public function table(Closure $cb): self
    {
        $cb->call($this, $this->table);
        return $this;
    }

    public function batch(Closure $cb): self
    {
        $cb->call($this, $this->batch);
        return $this;
    }

    public function quick(Closure $cb): self
    {
        $cb->call($this, $this->quick);
        return $this;
    }

    public function filter(Closure $cb): self
    {
        $cb->call($this, $this->filter);
        return $this;
    }


    /**
     * 设置标题
     * @param string $title
     * @return $this
     */
    public function setTitle(string $title): self
    {
        $this->title = $title;
        return $this;
    }

    public function __get($attr)
    {
        return $this->{$attr} ?? '';
    }


    protected function structQuery(): array
    {
        $columns = [];
        $this->table->visibleCols()->each(function (Column $column) use (&$columns) {
            $columns[] = $column->struct();
        });

        // if batchAction => selection True
        // if exportable => selection True
        // if selection & !pk => Selection Disable
        // 支持批处理, 开启选择器
        if (count($this->batch->struct())) {
            $this->table->enableSelection();
        }
        if ($this->filter->getEnableExport()) {
            $this->table->enableSelection();
        }

        return [
            'type'    => 'grid',
            'url'     => $this->pyRequest()->url(),
            'title'   => $this->title ?: '-',
            'batch'   => $this->batch->struct(),
            'scopes'  => $this->filter->getScopesStruct(),
            'scope'   => $this->filter->getCurrentScope() ? $this->filter->getCurrentScope()->value : '',
            'options' => [
                'page_sizes' => $this->table->pagesizeOptions,
                'selection'  => $this->table->enableSelection,
            ],
            'cols'    => $this->table->struct(),
        ];
    }

    protected function structFilter(): array
    {
        return [
            'actions' => $this->quick->struct(),
            'filter'  => $this->filter->struct(),
        ];
    }

    /**
     * 填充结构化的数据
     * @param Collection $collection
     * @param int $total
     * @return array
     */
    protected function structData(Collection $collection, int $total): array
    {
        $rows = $collection->map(function ($row) {
            $newRow = collect();
            $this->table->visibleCols()->each(function (Column $column) use ($row, $newRow) {
                $newRow->put($column->name, $column->fillVal($row));
            });
            return $newRow->toArray();
        });

        return [
            'list'  => $rows->toArray(),
            'total' => $total,
        ];
    }
}
