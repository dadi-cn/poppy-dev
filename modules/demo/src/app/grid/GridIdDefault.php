<?php

namespace Demo\App\Grid;

use Poppy\MgrApp\Classes\Grid\GridBase;
use Poppy\MgrApp\Classes\Grid\Tools\Interactions;
use Poppy\MgrApp\Classes\Table\TablePlugin;

class GridIdDefault extends GridBase
{
    public string $title = '主键列默认返回';

    /**
     * @inheritDoc
     */
    public function table(TablePlugin $table)
    {
        $table->add('title', '标题')->quickTitle();
        $table->add('created_at')->quickDatetime();
    }

    public function batch(Interactions $actions)
    {
        $actions->request('批量操作', route('demo:api.mgr_app.grid_request', ['success']));
    }
}
