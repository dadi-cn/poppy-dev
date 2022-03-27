<?php

namespace Poppy\MgrPage\Classes\Grid\Displayer;

use Illuminate\Database\Eloquent\Model;
use Poppy\MgrPage\Classes\Grid;
use Poppy\MgrPage\Classes\Grid\Column;
use stdClass;

abstract class AbstractDisplayer
{
    /**
     * @var Model
     */
    public $row;

    /**
     * @var Grid
     */
    protected $grid;

    /**
     * @var Column
     */
    protected $column;

    /**
     * @var mixed
     */
    protected $value;

    /**
     * Create a new displayer instance.
     *
     * @param mixed    $value
     * @param Grid     $grid
     * @param Column   $column
     * @param stdClass $row
     */
    public function __construct($value, Grid $grid, Column $column, $row)
    {
        $this->value  = $value;
        $this->grid   = $grid;
        $this->column = $column;
        $this->row    = $row;
    }

    /**
     * @return mixed
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * @return Grid
     */
    public function getGrid()
    {
        return $this->grid;
    }

    /**
     * @return Column
     */
    public function getColumn()
    {
        return $this->column;
    }


    /**
     * Get key of current row.
     *
     * @return mixed
     */
    public function getKey()
    {
        return $this->row->{$this->grid->getKeyName()};
    }

    /**
     * Get url path of current resource.
     *
     * @return string
     */
    public function getResource()
    {
        return $this->grid->resource();
    }

    /**
     * Display method.
     *
     * @return mixed
     */
    abstract public function display();
}
