<?php

namespace Demo\App\Grid;

use Demo\Models\DemoWebapp;
use Poppy\MgrApp\Classes\Grid\GridBase;
use Poppy\MgrApp\Classes\Table\Render\GridActions;
use Poppy\MgrApp\Classes\Table\TablePlugin;

/**
 * 按钮
 * @mixin DemoWebapp
 */
class GridButton extends GridBase
{
    /**
     * @inheritDoc
     */
    public function table(TablePlugin $table)
    {
        $table->add('id')->quickId();
        $table->add('handle', '操作')->asAction(function (GridActions $actions) {
            $row = $actions->getRow();

            if ($row->id % 8 === 0) {
                $actions->copy('Motion', 'Motion')->plain();
                $actions->request('Window Reload', route_url('demo:api.grid.motion', null, [
                    'type'   => 'window',
                    'action' => 'reload',
                ]))->info();
                $actions->request('Grid Reload', route_url('demo:api.grid.motion', null, [
                    'type'   => 'grid',
                    'action' => 'reload',
                ]))->primary();
                $actions->request('Grid Reset', route_url('demo:api.grid.motion', null, [
                    'type'   => 'grid',
                    'action' => 'reset',
                ]))->primary();
                $actions->request('Grid Filter', route_url('demo:api.grid.motion', null, [
                    'type'   => 'grid',
                    'action' => 'filter',
                ]))->primary();
            }
            else if ($row->id % 8 === 1) {
                $actions->request('成功', route('demo:api.grid.request', ['success']))->success();
                $actions->request('错误', route('demo:api.grid.request', ['error']))->danger();
                $actions->request('确认', route('demo:api.grid.request', ['success']))->info()->confirm();
            }
            else if ($row->id % 8 === 2) {
                $actions->page('Page & Form', route('demo:api.form.auto', ['field-text']), 'form');
                $actions->page('Page & Grid', route('demo:api.grid.auto', ['layout']), 'grid');
                $actions->page('Table', route('demo:api.table.ez'), 'table');
            }
            else if ($row->id % 8 === 3) {
                $actions->dialog('Dialog & Form', route('demo:api.form.auto', ['field-text']), 'form');
            }
            else if ($row->id % 8 === 4) {
                $actions->copy('复制', '可以复制的内容')->icon('mu:content_copy')->circle()->only();
                $actions->target('Target(百度)', 'https://www.baidu.com');
                $actions->iframe('Iframe', 'https://poppy-framework.com/');
            }
            else if ($row->id % 8 === 5) {
                $actions->styleDropdown(3);
                $actions->request('错误', route_url('demo:api.grid.request', ['error'], ['id' => data_get($row, 'id')]));
                $actions->request('成功', route_url('demo:api.grid.request', ['success'], ['id' => data_get($row, 'id')]));
                $actions->request('Disabled', route_url('demo:api.grid.request', ['success'], ['id' => data_get($row, 'id')]))->disabled();
                $actions->request('Plain', route_url('demo:api.grid.request', ['success'], ['id' => data_get($row, 'id')]))->plain();
                $actions->request('Primary', route_url('demo:api.grid.request', ['success'], ['id' => data_get($row, 'id')]))->primary();
                $actions->request('Plain', route_url('demo:api.grid.request', ['success'], ['id' => data_get($row, 'id')]))->primary()->plain();
                $actions->request('确认', route_url('demo:api.grid.request', ['success'], ['id' => data_get($row, 'id')]))->confirm();
                $actions->page('页面', route_url('demo:api.form.auto', ['field-color']), 'form');
            }
            else if ($row->id % 8 === 6) {
                $actions->styleDropdown(3, true)->quickIcon();
                $actions->request('错误', route_url('demo:api.grid.request', ['error'], ['id' => data_get($row, 'id')]))->icon('Close');
                $actions->request('成功', route_url('demo:api.grid.request', ['success'], ['id' => data_get($row, 'id')]))->icon('Aim');
                $actions->request('Disabled', route_url('demo:api.grid.request', ['success'], ['id' => data_get($row, 'id')]))->icon('Apple')->disabled();
                $actions->request('Plain', route_url('demo:api.grid.request', ['success'], ['id' => data_get($row, 'id')]))->icon('Avatar')->plain();
                $actions->request('Primary', route_url('demo:api.grid.request', ['success'], ['id' => data_get($row, 'id')]))->icon('Bell')->primary();
                $actions->request('Plain', route_url('demo:api.grid.request', ['success'], ['id' => data_get($row, 'id')]))->icon('Burger')->primary()->plain();
                $actions->request('确认', route_url('demo:api.grid.request', ['success'], ['id' => data_get($row, 'id')]))->icon('Brush')->confirm();
                $actions->page('页面', route_url('demo:api.form.auto', ['field-color']), 'form')->icon('Box');
            }
            else if ($row->id % 8 === 7) {
                $row = $actions->getRow();
                $actions->page($row->title, route('demo:api.form.auto', ['field-color']), 'form')->link();
            }
        });
    }

}
