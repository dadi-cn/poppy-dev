<?php

namespace Poppy\MgrApp\Classes\Grid\Column;

use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Contracts\Support\Renderable;
use Poppy\MgrApp\Classes\Grid\Query\QueryModel;
use function collect;
use function request;

trait HasHeader
{
    /**
     * @var Filter
     */
    public $filter;

    /**
     * @var array
     */
    protected $headers = [];

    /**
     * Add contents to column header.
     *
     * @param string|Renderable|Htmlable $header
     *
     * @return $this
     */
    public function addHeader($header)
    {
        if ($header instanceof Filter) {
            $header->setParent($this);
            $this->filter = $header;
        }

        $this->headers[] = $header;
        return $this;
    }

    /**
     * Add a binding based on filter to the model query.
     *
     * @param QueryModel $model
     */
    public function bindFilterQuery(QueryModel $model)
    {
        if ($this->filter) {
            $this->filter->addBinding(request($this->name), $model);
        }
    }

    /**
     * Render Column header.
     *
     * @return string
     */
    public function renderHeader()
    {
        return collect($this->headers)->map(function ($item) {
            if ($item instanceof Renderable) {
                return $item->render();
            }

            if ($item instanceof Htmlable) {
                return $item->toHtml();
            }

            return (string) $item;
        })->implode('');
    }

    public function getHeader()
    {
        return $this->headers;
    }

    /**
     * 添加搜索到列 Header
     * @return $this
     */
    protected function addFilter($type = null, $formal = null)
    {
        if (is_array($type)) {
            return $this->addHeader(new CheckFilter($type));
        }

        if (is_null($type)) {
            $type = 'equal';
        }

        if (in_array($type, ['equal', 'like', 'date', 'time', 'datetime'])) {
            return $this->addHeader(new InputFilter($type));
        }

        if ($type === 'range') {
            if (is_null($formal)) {
                $formal = 'equal';
            }

            return $this->addHeader(new RangeFilter($formal));
        }

        return $this;
    }
}
