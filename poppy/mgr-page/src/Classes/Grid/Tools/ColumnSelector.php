<?php

namespace Poppy\MgrPage\Classes\Grid\Tools;

use Illuminate\Support\Collection;
use Poppy\MgrPage\Classes\Grid;

class ColumnSelector extends AbstractTool
{
    const SELECT_COLUMN_NAME = '_columns_';

    /**
     * @var Grid
     */
    protected $grid;

    /**
     * @var array
     */
    protected static $ignoredColumns = [
        \Poppy\MgrPage\Classes\Grid\Column::NAME_SELECTOR,
        \Poppy\MgrPage\Classes\Grid\Column::NAME_ACTION,
    ];

    /**
     * Create a new Export button instance.
     *
     * @param Grid $grid
     */
    public function __construct(Grid $grid)
    {
        $this->grid = $grid;
    }

    /**
     * @inheritDoc
     *
     * @return string
     */
    public function render()
    {
        if (!$this->grid->showColumnSelector()) {
            return '';
        }

    }

    /**
     * @return Collection
     */
    protected function getGridColumns()
    {
        return $this->grid->columns()->map(function (\Poppy\MgrPage\Classes\Grid\Column $column) {
            $name = $column->name;

            if ($this->isColumnIgnored($name)) {
                return;
            }

            return [$name => $column->label];
        })->filter()->collapse();
    }

    /**
     * Is column ignored in column selector.
     *
     * @param string $name
     *
     * @return bool
     */
    protected function isColumnIgnored($name)
    {
        return in_array($name, static::$ignoredColumns);
    }

    /**
     * Ignore a column to display in column selector.
     *
     * @param string|array $name
     */
    public static function ignore($name)
    {
        static::$ignoredColumns = array_merge(static::$ignoredColumns, (array) $name);
    }
}
