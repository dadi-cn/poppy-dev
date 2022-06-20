<?php

namespace Demo\App\Grid;

use Poppy\MgrApp\Classes\Grid\GridBase;
use Poppy\MgrApp\Classes\Grid\Tools\Actions;
use Poppy\MgrApp\Classes\Widgets\TableWidget;

class GridIdDefault extends GridBase
{
    public string $title = '主键列默认返回';

    /**
     * @inheritDoc
     */
    public function table(TableWidget $table)
    {
        $table->add('title', '标题')->quickTitle();
        $table->add('created_at')->quickDatetime();
    }

    public function batch(Actions $actions)
    {
        $actions->request('批量操作', route('demo:api.mgr_app.grid_request', ['success']));
    }
}
