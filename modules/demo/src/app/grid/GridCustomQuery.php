<?php

namespace Demo\App\Grid;

use Poppy\MgrApp\Classes\Grid\GridBase;
use Poppy\MgrApp\Classes\Table\TablePlugin;

class GridCustomQuery extends GridBase
{
    public string $title = '自定义查询';

    /**
     * @inheritDoc
     */
    public function table(TablePlugin $table)
    {
        // 自定义样式
        $table->add('title', '标题')->quickTitle();
        $table->add('is_loading', '开关')->asOnOff();
        $table->add('created_at')->quickDatetime();
    }
}
