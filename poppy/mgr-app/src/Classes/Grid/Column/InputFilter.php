<?php

namespace Poppy\MgrApp\Classes\Grid\Column;

use Poppy\MgrApp\Classes\Grid\Query\QueryModel;

class InputFilter extends Filter
{
    /**
     * @var string
     */
    protected $type;

    /**
     * InputFilter constructor.
     *
     * @param string $type
     */
    public function __construct($type)
    {
        $this->type  = $type;
    }

    /**
     * Add a binding to the query.
     *
     * @param string          $value
     * @param QueryModel|null $model
     */
    public function addBinding($value, QueryModel $model)
    {
        if (empty($value)) {
            return;
        }

        if ($this->type == 'like') {
            $model->where($this->getColumnName(), 'like', "%{$value}%");

            return;
        }

        if (in_array($this->type, ['date', 'time'])) {
            $method = 'where' . ucfirst($this->type);
            $model->{$method}($this->getColumnName(), $value);

            return;
        }

        $model->where($this->getColumnName(), $value);
    }


}
