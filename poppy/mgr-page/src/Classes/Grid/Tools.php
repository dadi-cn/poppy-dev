<?php

namespace Poppy\MgrPage\Classes\Grid;

use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Support\Collection;
use Poppy\MgrPage\Classes\Actions\GridAction;
use Poppy\MgrPage\Classes\Grid;
use Poppy\MgrPage\Classes\Grid\Filter\AbstractFilter;
use Poppy\MgrPage\Classes\Grid\Tools\AbstractTool;
use Poppy\MgrPage\Classes\Grid\Tools\FilterButton;

class Tools extends AbstractFilter implements Renderable
{
    /**
     * Parent grid.
     *
     * @var Grid
     */
    protected $grid;

    /**
     * Collection of tools.
     *
     * @var Collection
     */
    protected $tools;

    /**
     * Create a new Tools instance.
     *
     * @param Grid $grid
     */
    public function __construct(Grid $grid)
    {
        $this->grid = $grid;

        $this->tools = new Collection();

        $this->appendDefaultTools();
    }

    /**
     * Append tools.
     *
     * @param AbstractTool|string $tool
     *
     * @return $this
     */
    public function append($tool)
    {
        if ($tool instanceof GridAction) {
            $tool->setGrid($this->grid);
        }

        $this->tools->push($tool);

        return $this;
    }

    /**
     * Prepend a tool.
     *
     * @param AbstractTool|string $tool
     *
     * @return $this
     */
    public function prepend($tool)
    {
        $this->tools->prepend($tool);

        return $this;
    }

    /**
     * Disable filter button.
     *
     * @param bool $disable
     * @return void
     */
    public function disableFilterButton(bool $disable = true)
    {
        $this->tools = $this->tools->map(function ($tool) use ($disable) {
            if ($tool instanceof FilterButton) {
                return $tool->disable($disable);
            }

            return $tool;
        });
    }

    /**
     * Disable batch actions.
     *
     * @param bool $disable
     * @return void
     */
    public function disableBatchActions(bool $disable = true)
    {
        $this->tools = $this->tools->map(function ($tool) use ($disable) {
            return $tool;
        });
    }

    /**
     * Render header tools bar.
     *
     * @return string
     */
    public function render()
    {
        return $this->tools->map(function ($tool) {
            if ($tool instanceof AbstractTool) {
                if (!$tool->allowed()) {
                    return '';
                }

                return $tool->setGrid($this->grid)->render();
            }

            if ($tool instanceof Renderable) {
                return $tool->render();
            }

            if ($tool instanceof Htmlable) {
                return $tool->toHtml();
            }

            return (string) $tool;
        })->implode(' ');
    }

    /**
     * Append default tools.
     */
    protected function appendDefaultTools()
    {
        $this->append(new FilterButton());
    }
}
