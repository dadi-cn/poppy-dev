<?php

namespace Poppy\Area\Http\Grid;

use Poppy\Area\Models\SysArea;
use Poppy\MgrApp\Classes\Grid\Column\Render\ActionsRender;
use Poppy\MgrApp\Classes\Grid\Column\Render\Render;
use Poppy\MgrApp\Classes\Grid\Tools\Actions;
use Poppy\MgrApp\Classes\Widgets\FilterWidget;
use Poppy\MgrApp\Classes\Widgets\TableWidget;
use Poppy\MgrApp\Http\Grid\GridBase;

class GridArea extends GridBase
{

    public string $title = '地区管理';

    /**
     */
    public function table(TableWidget $table)
    {
        $table->add('id', "ID")->quickId();
        $table->add('title', "名称");
        $table->action(function (ActionsRender $actions) {
            /** @var Render $this */
            $item = $this->getRow();
            $actions->quickIcon();
            $actions->page('编辑', route('py-area:api-backend.content.establish', $item->id), 'form')->icon('Edit');
            $actions->request('删除', route('py-area:api-backend.content.delete', $item->id))->icon('Close')->danger()->confirm();
        })->quickIcon(2);
    }


    public function filter(FilterWidget $filter)
    {
        $filter->like('title', '标题');
        $filter->equal('parent_id', '上级地区')->asSelect(SysArea::cityMgrTree(), '选择地区', true);
    }

    public function quick(Actions $actions)
    {
        $actions->page('新增地域', route('py-area:api-backend.content.establish'), 'form');
        $actions->progress('修复数据', route('py-area:api-backend.content.fix'));
    }
}
