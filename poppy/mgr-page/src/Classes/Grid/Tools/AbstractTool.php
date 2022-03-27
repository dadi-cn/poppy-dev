<?php

namespace Poppy\MgrPage\Classes\Grid\Tools;

use Illuminate\Contracts\Support\Renderable;
use Poppy\MgrPage\Classes\Grid;

abstract class AbstractTool implements Renderable
{
    /**
     * @var Grid
     */
    protected $grid;

    /**
     * @var bool
     */
    protected $disabled = false;

    /**
     * Toggle this button.
     *
     * @param bool $disable
     *
     * @return $this
     */
    public function disable(bool $disable = true)
    {
        $this->disabled = $disable;

        return $this;
    }

    /**
     * If the tool is allowed.
     */
    public function allowed()
    {
        return !$this->disabled;
    }

    /**
     * Set parent grid.
     *
     * @param Grid $grid
     *
     * @return $this
     */
    public function setGrid(Grid $grid)
    {
        $this->grid = $grid;

        return $this;
    }

    /**
     * @return Grid
     */
    public function getGrid()
    {
        return $this->grid;
    }

    /**
     * @inheritDoc
     */
    abstract public function render();

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->render();
    }
}
