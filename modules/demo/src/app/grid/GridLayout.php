<?php

namespace Demo\App\Grid;

use Poppy\Framework\Exceptions\ApplicationException;
use Poppy\MgrApp\Classes\Grid\Column\Render\ActionsRender;
use Poppy\MgrApp\Classes\Grid\Tools\Actions;
use Poppy\MgrApp\Classes\Widgets\FilterWidget;
use Poppy\MgrApp\Classes\Widgets\TableWidget;
use Poppy\MgrApp\Http\Grid\GridBase;
use function Clue\StreamFilter\fun;

class GridLayout extends GridBase
{
    public string $title = '布局';

    /**
     * @inheritDoc
     */
    public function table(TableWidget $table)
    {
        // 自定义样式
        $table->add('id', 'ID')->quickId()->sortable();
        $table->add('title', '标题')->quickTitle();
        $table->add('user.nickname', 'Nickname(联合查询)')->quickTitle();
        $table->add('created_at')->quickDatetime();
        $table->action(function (ActionsRender $actions) {
            $actions->styleIcon();
            $actions->request('错误', route('demo:api.mgr_app.grid_request', ['error']))->icon('Close')->danger();
            $actions->request('成功', route('demo:api.mgr_app.grid_request', ['success']))->icon('Check')->success();
            $actions->request('确认', route('demo:api.mgr_app.grid_request', ['success']))->confirm()->icon('QuestionFilled')->warning();
            $actions->request('Disabled', route('demo:api.mgr_app.grid_request', ['success']))->disabled()->icon('Minus');
            $actions->page('页面', route('demo:api.mgr_app.grid_form', ['detail']), 'form')->icon('Edit')->info();
        })->quickIcon(5, true);
    }


    /**
     * @inheritDoc
     */
    public function filter(FilterWidget $filter)
    {
        $filter->action(6, true);
        $filter->like('title', '标题')->width(4);
    }

    public function quick(Actions $actions)
    {
        $actions->page('快捷操作', route('demo:api.mgr_app.grid_form', ['detail']), 'form')->icon('Plus');
    }

    public function batch(Actions $actions)
    {
        $actions->request('批量操作', route('demo:api.mgr_app.grid_request', ['success']));
    }
}
