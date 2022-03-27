<?php

namespace Poppy\MgrPage\Classes\Grid\Concerns;

use Closure;
use Poppy\MgrPage\Classes\Grid;

trait HasActions
{
    /**
     * Callback for grid actions.
     *
     * @var Closure
     */
    protected $actionsCallback;

    /**
     * Actions column display class.
     *
     * @var string
     */
    protected $actionsClass;


    /**
     * @var array
     */
    protected $batchActions;

    /**
     * Set grid action callback.
     *
     * @param Closure|string $actions
     *
     * @return $this
     */
    public function actions($actions)
    {
        if ($actions instanceof Closure) {
            $this->actionsCallback = $actions;
        }

        return $this;
    }

    /**
     * Get action display class.
     *
     * @return string
     */
    public function getActionClass()
    {
        if ($this->actionsClass) {
            return $this->actionsClass;
        }

        return \Poppy\MgrPage\Classes\Grid\Displayer\Actions::class;
    }

    /**
     * @param string $actionClass
     *
     * @return $this
     */
    public function setActionClass(string $actionClass)
    {
        if (is_subclass_of($actionClass, \Poppy\MgrPage\Classes\Grid\Displayer\Actions::class)) {
            $this->actionsClass = $actionClass;
        }

        return $this;
    }

    /**
     * Set grid batch-action callback.
     *
     *
     * @return $this
     */
    public function batchActions(array $array): self
    {
        $this->batchActions = $array;
        return $this;
    }

    /**
     * @param bool $disable
     *
     * @return Grid|mixed
     */
    public function disableBatchActions(bool $disable = true)
    {
        $this->tools->disableBatchActions($disable);

        return $this->option('show_row_selector', !$disable);
    }


    /**
     * Render create button for grid.
     *
     * @return string
     */
    public function renderBatchActions(): string
    {
        $append = '';
        foreach ($this->batchActions as $button) {
            $append .= $button->render();
        }
        return $append;
    }

    /**
     * Render create button for grid.
     *
     * @return string
     */
    public function skeletonBatchActions(): string
    {
        $append = '';
        foreach ($this->batchActions as $button) {
            $append .= $button->render();
        }
        return $append;
    }
}
