<?php

namespace Demo\App\Grid;


use Poppy\MgrApp\Classes\Filter\FilterPlugin;
use Poppy\MgrApp\Classes\Grid\GridBase;
use Poppy\MgrApp\Classes\Grid\Tools\Interactions;
use Poppy\MgrApp\Classes\Table\Render\GridActions;
use Poppy\MgrApp\Classes\Table\TablePlugin;

class GridLayout extends GridBase
{
    public string $title = '布局';

    /**
     * @inheritDoc
     */
    public function table(TablePlugin $table)
    {
        // 自定义样式
        $table->add('id', 'ID')->quickId()->sortable();
        $table->add('title', '标题');
        $table->add('user.nickname', 'Nickname(联合查询)')->quickTitle();
        $table->add('created_at')->quickDatetime();
        $table->add('handle', '操作')->asAction(function (GridActions $actions) {
            $actions->quickIcon();
            $row = $actions->getRow();
            $actions->request('错误', route('demo:api.grid.request', ['error']))->icon('Close')->danger();
            $actions->request('成功', route('demo:api.grid.request', ['success']))->icon('Check')->success();
            $actions->request('确认', route('demo:api.grid.request', ['success']))->confirm()->icon('QuestionFilled')->warning();
            $actions->request('Disabled', route('demo:api.grid.request', ['success']))->disabled()->icon('Minus');
            $actions->page('页面', route('demo:api.form.auto', ['field-color']), 'form')->icon('Edit')->info();
            $actions->copy('复制', '复制自定义的内容' . $row['id']);
        })->quickIcon(6);
    }


    /**
     * @inheritDoc
     */
    public function filter(FilterPlugin $filter)
    {
        $filter->action(6, true);
        $filter->like('title', '标题')->width(4);
    }

    public function quick(Interactions $actions)
    {
        $actions->page('快捷操作', route('demo:api.form.auto', ['field-color']), 'form')->icon('Plus');
    }

    public function batch(Interactions $actions)
    {
        $actions->request('批量操作', route('demo:api.grid.request', ['success']));
    }
}
