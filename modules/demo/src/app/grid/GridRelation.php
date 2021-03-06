<?php

namespace Demo\App\Grid;

use Poppy\MgrApp\Classes\Grid\GridBase;
use Poppy\MgrApp\Classes\Table\TablePlugin;

class GridRelation extends GridBase
{
    public string $title = '关联关系';

    /**
     * @inheritDoc
     */
    public function table(TablePlugin $table)
    {
        // 自定义样式
        $table->add('title', '标题')->quickTitle();
        $table->add('user.nickname', 'Nickname(User)')->quickTitle();
        $table->add('user.id', 'ID(User)')->quickId()->sortable();
        $table->add('comment:id', '评论(Comment)')->quickTitle();
        $table->add('created_at')->quickDatetime();
    }
}
