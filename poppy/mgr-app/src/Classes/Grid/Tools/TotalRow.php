<?php

namespace Poppy\MgrApp\Classes\Grid\Tools;

use Illuminate\Database\Query\Builder;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Poppy\MgrApp\Classes\Grid\Column\Column;
use function view;

class TotalRow extends AbstractTool
{
    /**
     * @var Builder
     */
    protected $query;

    /**
     * @var array
     */
    protected $columns;

    /**
     * @var Collection
     */
    protected $visibleColumns;

    /**
     * TotalRow constructor.
     *
     * @param Builder $query
     * @param array   $columns
     */
    public function __construct($query, array $columns)
    {
        $this->query = $query;

        $this->columns = $columns;
    }

    /**
     * Get total value of current column.
     *
     * @param string $column
     * @param mixed  $display
     *
     * @return mixed
     */
    protected function total($column, $display = null)
    {
        if (!is_callable($display) && !is_null($display)) {
            return $display;
        }

        $sum = $this->query->sum($column);

        if (is_callable($display)) {
            return call_user_func($display, $sum);
        }

        return $sum;
    }

    /**
     * @param Collection $columns
     */
    public function setVisibleColumns($columns)
    {
        $this->visibleColumns = $columns;
    }

    /**
     * @return Collection|static
     */
    public function getVisibleColumns()
    {
        if ($this->visibleColumns) {
            return $this->visibleColumns;
        }

        return $this->getGrid()->visibleColumns();
    }

    /**
     * Render total-row.
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function render()
    {
        $columns = $this->getVisibleColumns()->map(function (Column $column) {
            $name = $column->name;

            $total = '';

            if (Arr::has($this->columns, $name)) {
                $total = $this->total($name, Arr::get($this->columns, $name));
            }

            return [
                'value' => $total,
            ];
        });

        return view('py-system::tpl.grid.total-row', compact('columns'));
    }
}
