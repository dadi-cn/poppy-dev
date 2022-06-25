<?php

namespace Demo\Models\Queries;

use Demo\Models\DemoWebapp;
use Eloquent;
use Illuminate\Support\Collection;
use Poppy\MgrApp\Classes\Grid\Query\QueryCustom;

/**
 * \Poppy\PoppyCoreDemo
 * @mixin Eloquent
 */
class QueryDemoWebapp extends QueryCustom
{

    public function get(): Collection
    {
        $Object = DemoWebapp::whereRaw('id % 3 =0');
        $this->total = $Object->count();
        return $Object->take($this->pagesize)->offset($this->pageOffset)->get();
    }
}