<?php

namespace Demo\App\Filter;

use Demo\Models\DemoUser;
use Poppy\MgrApp\Classes\Filter\FilterPlugin;
use Poppy\MgrApp\Classes\Grid\GridBase;
use Poppy\MgrApp\Classes\Table\TablePlugin;

class FilterNotEqual extends GridBase
{
    public string $title = '不等于';

    /**
     * @inheritDoc
     */
    public function table(TablePlugin $table)
    {
        $table->add('id', 'ID')->quickId()->sortable();
        $table->add('user.id', 'UID')->quickId()->align('center');
        $table->add('title', '标题')->quickTitle();
    }


    /**
     * @inheritDoc
     */
    public function filter(FilterPlugin $filter)
    {
        $filter->action();
        $filter->notEqual('id', 'ID')->asText();
        $filter->notEqual('account_id', 'Uid!=(Select)')->asSelect(DemoUser::where('id', '<', 10)->pluck('id', 'id')->toArray(), '选择用户');
    }
}
