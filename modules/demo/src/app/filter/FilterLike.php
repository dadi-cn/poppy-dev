<?php

namespace Demo\App\Filter;

use Poppy\Framework\Exceptions\ApplicationException;
use Poppy\MgrApp\Classes\Widgets\FilterWidget;
use Poppy\MgrApp\Classes\Widgets\TableWidget;
use Poppy\MgrApp\Http\Grid\GridBase;

class FilterLike extends GridBase
{
    public string $title = '查询(模糊/开始/末尾)';

    /**
     * @inheritDoc
     */
    public function table(TableWidget $table)
    {
        $table->add('id', 'ID')->quickId();
        $table->add('title', '标题')->quickTitle();
        $table->add('description', '描述')->quickTitle();
        $table->add('note', '备注(末尾匹配)')->ellipsis();
    }


    /**
     * @inheritDoc
     */
    public function filter(FilterWidget $filter)
    {
        $filter->action();
        $filter->startsWith('title', '标题(开始匹配)');
        $filter->like('description', '描述(模糊查询)');
        $filter->endsWith('note', '备注(末尾匹配)');
    }
}
