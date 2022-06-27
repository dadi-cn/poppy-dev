<?php

namespace Demo\App\Grid;

use Poppy\MgrApp\Classes\Grid\GridBase;
use Poppy\MgrApp\Classes\Table\Render\GridActions;
use Poppy\MgrApp\Classes\Table\TablePlugin;

/**
 * 按钮
 */
class GridButtonDropdown extends GridBase
{
    /**
     * @inheritDoc
     */
    public function table(TablePlugin $table)
    {
        $table->add('id')->quickId();
        $table->add('title', '标题(Link模式, 支持Action)')->width(100)->asAction(function (GridActions $actions) {
            $row = $actions->getRow();
            $actions->page($row->title, route('demo:api.form.auto', ['field-color']), 'form')->link();
        });
        $table->add('dropdown', '操作')->asAction(function (GridActions $actions) {
            $row = $actions->getRow();
            $actions->styleDropdown(3);
            $actions->request('错误', route_url('demo:api.grid.request', ['error'], ['id' => data_get($row, 'id')]));
            $actions->request('成功', route_url('demo:api.grid.request', ['success'], ['id' => data_get($row, 'id')]));
            $actions->request('Disabled', route_url('demo:api.grid.request', ['success'], ['id' => data_get($row, 'id')]))->disabled();
            $actions->request('Plain', route_url('demo:api.grid.request', ['success'], ['id' => data_get($row, 'id')]))->plain();
            $actions->request('Primary', route_url('demo:api.grid.request', ['success'], ['id' => data_get($row, 'id')]))->primary();
            $actions->request('Plain', route_url('demo:api.grid.request', ['success'], ['id' => data_get($row, 'id')]))->primary()->plain();
            $actions->request('确认', route_url('demo:api.grid.request', ['success'], ['id' => data_get($row, 'id')]))->confirm();
            $actions->page('页面', route_url('demo:api.form.auto', ['field-color']), 'form');
        });
        $table->add('icon-dropdown', '操作')->asAction(function (GridActions $actions) {
            $row = $actions->getRow();
            $actions->styleDropdown(3, true)->quickIcon();
            $actions->request('错误', route_url('demo:api.grid.request', ['error'], ['id' => data_get($row, 'id')]))->icon('Close');
            $actions->request('成功', route_url('demo:api.grid.request', ['success'], ['id' => data_get($row, 'id')]))->icon('Aim');
            $actions->request('Disabled', route_url('demo:api.grid.request', ['success'], ['id' => data_get($row, 'id')]))->icon('Apple')->disabled();
            $actions->request('Plain', route_url('demo:api.grid.request', ['success'], ['id' => data_get($row, 'id')]))->icon('Avatar')->plain();
            $actions->request('Primary', route_url('demo:api.grid.request', ['success'], ['id' => data_get($row, 'id')]))->icon('Bell')->primary();
            $actions->request('Plain', route_url('demo:api.grid.request', ['success'], ['id' => data_get($row, 'id')]))->icon('Burger')->primary()->plain();
            $actions->request('确认', route_url('demo:api.grid.request', ['success'], ['id' => data_get($row, 'id')]))->icon('Brush')->confirm();
            $actions->page('页面', route_url('demo:api.form.auto', ['field-color']), 'form')->icon('Box');
        });
    }
}
