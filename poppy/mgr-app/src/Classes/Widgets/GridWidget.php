<?php

namespace Poppy\MgrApp\Classes\Widgets;

use Eloquent;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Response;
use Poppy\Framework\Classes\Resp;
use Poppy\Framework\Classes\Traits\PoppyTrait;
use Poppy\MgrApp\Classes\Grid\Column\Column;
use Poppy\MgrApp\Classes\Grid\Exporter;
use Poppy\MgrApp\Classes\Grid\Exporters\AbstractExporter;
use Poppy\MgrApp\Classes\Grid\GridBase;
use Poppy\MgrApp\Classes\Grid\Query\Query;
use Poppy\MgrApp\Classes\Grid\Query\QueryFactory;
use Poppy\MgrApp\Classes\Grid\Tools\Actions;
use Poppy\MgrApp\Classes\Traits\UseWidgetUtil;
use function collect;
use function input;

/**
 * @property-read string $title 标题
 */
class GridWidget
{
    use PoppyTrait;
    use UseWidgetUtil;

    /**
     * @var FilterWidget
     */
    protected FilterWidget $filter;

    /**
     * Export driver.
     *
     * @var string
     */
    protected string $exporter = 'csv';

    /**
     * 列表模型实例
     *
     * @var Query
     */
    protected Query $query;

    /**
     * 列组件
     * @var TableWidget
     */
    protected TableWidget $table;

    /**
     * 右上角快捷操作
     * @var Actions
     */
    private Actions $quick;

    /**
     * 左下角快捷操作
     * @var Actions
     */
    private Actions $batch;

    /**
     * 标题
     * @var string
     */
    private string $title = '';

    /**
     * Create a new grid instance.
     *
     * @param Model|Eloquent|Query|string $model
     */
    public function __construct($model)
    {
        $this->query  = QueryFactory::create($model);
        $this->filter = new FilterWidget();
        $this->quick  = (new Actions())->default(['plain', 'primary']);
        $this->batch  = (new Actions())->default(['info', 'plain']);
        $this->table  = new TableWidget();
    }

    /**
     * 设置 Grid 的导出方式, 支持 csv, excel , 并可以通过 Extend 进行自定义
     * @param $exporter
     * @return $this
     */
    public function exporter($exporter): self
    {
        $this->exporter = $exporter;
        return $this;
    }

    /**
     * 获取模型
     * @return Query
     */
    public function model(): Query
    {
        return $this->query;
    }

    /**
     * @param string $grid_class
     */
    public function setLists(string $grid_class)
    {
        if (!class_exists($grid_class)) {
            sys_error('mgr-app', __CLASS__, 'Grid Class `' . $grid_class . '` Not Exists.');
        }

        /** @var GridBase $List */
        $List = new $grid_class();

        /* 设置标题
         * ---------------------------------------- */
        $this->title = $List->title;
        $List->table($this->table);

        // 为请求添加默认列
        if ($this->query->getPrimaryKey()) {
            $this->table->add($this->query->getPrimaryKey(), 'ID', false)->quickId();
        }

        $List->quick($this->quick);
        $List->filter($this->filter);
        $List->batch($this->batch);
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

    /**
     * 返回相应
     *
     * @return JsonResponse|RedirectResponse|Response
     */
    public function resp()
    {
        if ($this->queryHas('export')) {
            $type = $this->queryAfter('export');
            $this->queryExport($type);
        }

        if ($this->queryHas('edit')) {
            return $this->queryEdit();
        }

        $resp = [];
        if ($this->queryHas('data')) {
            $resp = array_merge($resp, $this->queryData());
        }
        if ($this->queryHas('struct')) {
            $resp = array_merge($resp, $this->queryStruct());
            $resp = array_merge($resp, $this->queryFilter());
        }
        if ($this->queryHas('filter')) {
            $resp = array_merge($resp, $this->queryFilter());
        }

        return Resp::success(input('_query') ?: '', $resp);
    }

    public function __get($attr)
    {
        return $this->{$attr} ?? '';
    }

    /**
     * 导出请求
     * @param string $scope
     */
    protected function queryExport(string $scope = 'page')
    {
        // clear output buffer.
        if (ob_get_length()) {
            ob_end_clean();
        }

        $this->query->usePaginate(false);

        $this->getExporter($scope)->export();
    }

    /**
     * @param string $scope
     * @return AbstractExporter
     */
    protected function getExporter(string $scope): AbstractExporter
    {
        return (new Exporter($this->query, $this->filter, $this->table, $this->title))->resolve($this->exporter)->withScope($scope);
    }

    private function queryStruct(): array
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
            'cols'    => $columns,
            'pk'      => $this->query->getPrimaryKey(),
        ];
    }

    private function queryFilter(): array
    {
        return [
            'actions' => $this->quick->struct(),
            'filter'  => $this->filter->struct(),
        ];
    }

    private function queryEdit()
    {
        $pk    = input('_pk');
        $field = input('_field');
        $value = input('_value');
        if (!$this->query->edit($pk, $field, $value)) {
            return Resp::error('修改失败');
        }
        return Resp::success('修改成功');
    }

    /**
     * 查询并返回数据
     */
    private function queryData(): array
    {
        // 获取模型数据
        $collection = $this->query->prepare($this->filter, $this->table)->get();


        $rows = $collection->map(function ($row) {
            $newRow = collect();
            $this->table->visibleCols()->each(function (Column $column) use ($row, $newRow) {
                $newRow->put(
                    $column->name,
                    $column->fillVal($row)
                );
            });
            return $newRow->toArray();
        });

        return [
            'list'  => $rows->toArray(),
            'total' => $this->query->total(),
        ];
    }
}
