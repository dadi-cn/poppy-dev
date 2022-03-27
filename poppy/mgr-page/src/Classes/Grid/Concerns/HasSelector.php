<?php

namespace Poppy\MgrPage\Classes\Grid\Concerns;

use Closure;
use Illuminate\Contracts\View\Factory;
use Illuminate\View\View;
use Poppy\MgrPage\Classes\Grid\Tools\Selector;

trait HasSelector
{
    /**
     * @var Selector
     */
    protected $selector;

    /**
     * @param Closure $closure
     *
     * @return $this
     */
    public function selector(Closure $closure)
    {
        $this->selector = new Selector();

        call_user_func($closure, $this->selector);

        $this->header(function () {
            return $this->renderSelector();
        });

        return $this;
    }

    /**
     * Render grid selector.
     *
     * @return Factory|View|string
     */
    public function renderSelector()
    {
        return $this->selector->render();
    }

    /**
     * Apply selector query to grid model query.
     *
     * @return $this
     */
    protected function applySelectorQuery()
    {
        if (is_null($this->selector)) {
            return $this;
        }

        $active = Selector::parseSelected();

        $this->selector->getSelectors()->each(function ($selector, $column) use ($active) {
            if (!array_key_exists($column, $active)) {
                return;
            }

            $values = $active[$column];

            if ($selector['type'] == 'one') {
                $values = current($values);
            }

            if (is_null($selector['query'])) {
                $this->model()->whereIn($column, $values);
            }
            else {
                call_user_func($selector['query'], $this->model(), $values);
            }
        });

        return $this;
    }
}
