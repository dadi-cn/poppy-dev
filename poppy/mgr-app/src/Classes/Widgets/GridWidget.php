<?php

namespace Poppy\MgrApp\Classes\Widgets;

use Eloquent;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Response;
use Poppy\Framework\Classes\Resp;
use Poppy\MgrApp\Classes\Grid\Exporter;
use Poppy\MgrApp\Classes\Grid\Exporters\AbstractExporter;
use Poppy\MgrApp\Classes\Grid\GridBase;
use Poppy\MgrApp\Classes\Grid\GridPlugin;
use Poppy\MgrApp\Classes\Grid\Query\Query;
use Poppy\MgrApp\Classes\Grid\Query\QueryFactory;
use function input;

/**
 * Grid 插件
 */
final class GridWidget extends GridPlugin
{

    /**
     * 列表模型实例
     *
     * @var Query
     */
    protected Query $query;

    /**
     * Export driver.
     *
     * @var string
     */
    protected string $exporter = 'csv';

    /**
     * Create a new grid instance.
     *
     * @param Model|Eloquent|Query|string $model
     */
    public function __construct($model)
    {
        $this->query = QueryFactory::create($model);
        parent::__construct();
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
     * @return GridWidget
     */
    public function setLists(string $grid_class):self
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
        return $this;
    }

    /**
     * 返回相应
     *
     * @return JsonResponse|RedirectResponse|Response
     */
    public function resp()
    {
        $query = input('_query');
        if ($this->queryHas($query, 'export')) {
            $type = $this->queryAfter($query, 'export');
            $this->queryExport($type);
        }

        if ($this->queryHas($query, 'edit')) {
            return $this->queryEdit();
        }

        $resp = [];
        if ($this->queryHas($query, 'data')) {
            $resp = array_merge($resp, $this->structData());
        }
        if ($this->queryHas($query, 'frame')) {
            $resp = array_merge($resp, $this->structQuery());
            $resp = array_merge($resp, $this->structFilter());
        }
        if ($this->queryHas($query, 'filter')) {
            $resp = array_merge($resp, $this->structFilter());
        }

        return Resp::success(input('_query') ?: '', $resp);
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
     * @param string $scope
     * @return AbstractExporter
     */
    protected function getExporter(string $scope): AbstractExporter
    {
        return (new Exporter($this->query, $this->filter, $this->table, $this->title))->resolve($this->exporter)->withScope($scope);
    }

    /**
     * 导出请求
     * @param string $scope
     */
    private function queryExport(string $scope = 'page')
    {
        // clear output buffer.
        if (ob_get_length()) {
            ob_end_clean();
        }

        $this->query->usePaginate(false);

        $this->getExporter($scope)->export();
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
}
