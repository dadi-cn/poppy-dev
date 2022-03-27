<?php

namespace Demo\Http\Lists;

use Closure;
use Poppy\Framework\Exceptions\ApplicationException;
use Poppy\MgrPage\Classes\Grid\Filter;
use Poppy\MgrPage\Classes\Grid\ListBase;

class ListPoppyUser extends ListBase
{
    /**
     * @inheritDoc
     * @throws ApplicationException
     */
    public function columns()
    {
        // 自定义样式
        $this->column('pam.username', 'UserName')->width(100);
    }


    /**
     * @inheritDoc
     * @return Closure
     */
    public function filter(): Closure
    {
        return function (Filter $filter) {
            $filter->column(1, function (Filter $filter) {
                $filter->like('username', 'username');
            });
            $filter->column(1, function (Filter $filter) {
                $filter->area('area', 'area');
            });
            $filter->column(2, function (Filter $filter) {
                $filter->betweenDate('id', 'Between')->withTime();
            });
            $filter->column(2, function (Filter $filter) {
                $filter->lt('datetime', 'Datetime')->datetime();
            });
            $filter->column(2, function (Filter $filter) {
                $filter->lt('date', 'Date')->date();
            });
            $filter->column(2, function (Filter $filter) {
                $filter->lt('time', 'Time')->time();
            });
        };
    }
}
