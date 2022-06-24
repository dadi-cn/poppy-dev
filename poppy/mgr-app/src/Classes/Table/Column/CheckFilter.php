<?php

namespace Poppy\MgrApp\Classes\Table\Column;

use Poppy\MgrApp\Classes\Grid\Query\QueryModel;

class CheckFilter extends Filter
{
    /**
     * @var array
     */
    protected $options;

    /**
     * CheckFilter constructor.
     *
     * @param array $options
     */
    public function __construct(array $options)
    {
        $this->options = $options;
    }

    /**
     * Add a binding to the query.
     *
     * @param array      $value
     * @param QueryModel $model
     */
    public function addBinding($value, QueryModel $model)
    {
        if (empty($value)) {
            return;
        }

        $model->whereIn($this->getColumnName(), $value);
    }
}
