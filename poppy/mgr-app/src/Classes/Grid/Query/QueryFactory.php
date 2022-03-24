<?php

namespace Poppy\MgrApp\Classes\Grid\Query;

use Illuminate\Database\Eloquent\Model;
use Poppy\Framework\Exceptions\ApplicationException;

class QueryFactory
{

    /**
     * 返回查询对象
     * @param string|mixed $model
     * @return Query
     * @throws ApplicationException
     */
    public static function create($model = null): Query
    {
        if ($model instanceof Model) {
            return new QueryModel($model);
        } else {
            if ($model instanceof Query) {
                return $model;
            }
            if (is_string($model)) {
                $obj = new $model;
                if (!($obj instanceof Query)) {
                    throw new ApplicationException("Type of {$model} is not subclass of Query");
                }
                return $obj;
            }
            throw new ApplicationException("Type of {$model} is error of Query");
        }
    }
}
