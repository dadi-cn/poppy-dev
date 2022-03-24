<?php

namespace Poppy\MgrApp\Classes\Grid\Tools;

use Poppy\MgrApp\Classes\Widgets\GridWidget;

abstract class AbstractTool
{
    /**
     * @var GridWidget
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
     * @param GridWidget $grid
     *
     * @return $this
     */
    public function setGrid(GridWidget $grid)
    {
        $this->grid = $grid;

        return $this;
    }

    /**
     * @return GridWidget
     */
    public function getGrid()
    {
        return $this->grid;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->render();
    }
}
