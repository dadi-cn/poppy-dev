<?php

namespace Poppy\MgrApp\Classes\Grid;

use Poppy\MgrApp\Classes\Contracts\Queryable;
use Poppy\MgrApp\Classes\Filter\FilterPlugin;
use Poppy\MgrApp\Classes\Grid\Exporters\AbstractExporter;
use Poppy\MgrApp\Classes\Grid\Exporters\CsvExporter;
use Poppy\MgrApp\Classes\Table\TablePlugin;

/**
 * 导出工具
 */
class Exporter
{
    public const TYPE_CSV = 'csv';

    /**
     * Export scope constants.
     */
    public const SCOPE_ALL    = 'all';        // 所有数据, 看需要是否返回, All 会比较敏感, 不建议开启
    public const SCOPE_PAGE   = 'page';       // 查询当前页数据,使用分页, 使用查询条件
    public const SCOPE_QUERY  = 'query';      // 查询条件下所有数据
    public const SCOPE_SELECT = 'select';     // 根据 PK, 返回所有的查询数据

    /**
     * Available exporter drivers.
     *
     * @var array
     */
    protected static array $drivers = [
        self::TYPE_CSV => CsvExporter::class,
    ];


    protected Queryable $model;

    protected FilterPlugin $filter;

    protected TablePlugin $column;

    /**
     * @var string
     */
    private string $title;

    /**
     * 扩展新实例
     */
    public function __construct(Queryable $model, FilterPlugin $filterWidget, TablePlugin $columnWidget, $title = '')
    {
        $this->model  = $model;
        $this->filter = $filterWidget;
        $this->column = $columnWidget;
        $this->title  = $title;
        $this->model->usePaginate(false);
    }

    /**
     * Extends new exporter driver.
     *
     * @param $driver
     * @param $extend
     */
    public static function extend($driver, $extend)
    {
        static::$drivers[$driver] = $extend;
    }

    /**
     * 获取导出工具
     * @param string $driver
     * @return AbstractExporter
     */
    public function resolve(string $driver): AbstractExporter
    {
        if (!array_key_exists($driver, static::$drivers)) {
            return $this->getDefaultExporter();
        }

        return new static::$drivers[$driver]($this->model, $this->filter, $this->column, $this->title);
    }

    /**
     * 获取默认的导出工具
     * @return CsvExporter
     */
    public function getDefaultExporter(): CsvExporter
    {
        return new CsvExporter($this->model, $this->filter, $this->column, $this->title);
    }
}
