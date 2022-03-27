<?php

namespace Poppy\MgrPage\Classes\Grid\Displayer;

use Closure;
use Poppy\MgrPage\Classes\Grid\Tools\BaseButton;

class Actions extends AbstractDisplayer
{
    /**
     * @var array
     */
    protected $appends = [];

    /**
     * @var array
     */
    protected $prepends = [];

    /**
     * Default actions.
     *
     * @var array
     */
    protected $actions = [];

    /**
     * @var string
     */
    protected $resource;

    /**
     * Append a action.
     *
     * @param array|string $action
     *
     * @return $this
     */
    public function append($action): self
    {
        if (is_array($action)) {
            foreach ($action as $act) {
                $this->append($act);
            }
        }
        else {
            array_push($this->appends, $action);
        }
        return $this;
    }


    /**
     * @inheritDoc
     */
    public function display($callback = null): string
    {
        if ($callback instanceof Closure) {
            $callback->call($this, $this);
        }

        $actions = [];
        foreach ($this->appends as $append) {
            if ($append instanceof BaseButton) {
                $actions[] = $append->render();
            }
        }

        return implode('', $actions);
    }
}
