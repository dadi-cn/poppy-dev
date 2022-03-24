<?php

namespace Demo\App\Grid;

use Poppy\MgrApp\Classes\Grid\Tools\Actions;
use Poppy\MgrApp\Classes\Widgets\TableWidget;
use Poppy\MgrApp\Http\Grid\GridBase;

class GridIdDefault extends GridBase
{
    public string $title = '主键列默认返回';

    /**
     * @inheritDoc
     */
    public function table(TableWidget $table)
    {
        // 自定义样式
        $table->add('title', '标题')->quickTitle();
        $table->add('user.nickname', 'Nickname(联合查询)')->quickTitle();
        $table->add('created_at')->quickDatetime();
    }

    public function batch(Actions $actions)
    {
        $actions->request('批量操作', route('demo:api.mgr_app.grid_request', ['success']));
    }
}
