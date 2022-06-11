<?php

namespace Poppy\MgrApp\Classes\Grid\Query;

use Illuminate\Database\Eloquent\Model;

class QueryFactory
{

    /**
     * 返回查询对象
     * @param string|mixed $model
     * @return Query
     */
    public static function create($model = null): Query
    {
        if ($model instanceof Model) {
            return new QueryModel($model);
        }
        else {
            if ($model instanceof Query) {
                return $model;
            }
            if (is_string($model)) {
                $obj = new $model;
                if (!($obj instanceof Query)) {
                    sys_error('mgr-app', __CLASS__, "Type of {$model} is not subclass of Query");
                }
                return $obj;
            }
            sys_error('mgr-app', __CLASS__, "Type of {$model} is error of Query");
        }
    }
}
