<?php

namespace Demo\App\Grid;

use Poppy\Framework\Exceptions\ApplicationException;
use Poppy\MgrApp\Classes\Grid\Column\Render\ActionsRender;
use Poppy\MgrApp\Classes\Widgets\TableWidget;
use Poppy\MgrApp\Http\Grid\GridBase;

/**
 * 按钮
 */
class GridButtonIconDropdown extends GridBase
{
    /**
     * @inheritDoc
     */
    public function table(TableWidget $table)
    {
        $table->add('id');
        $table->action(function (ActionsRender $actions) {
            $row = $actions->getRow();
            $actions->styleDropdown(3, true)->styleIcon();
            $actions->request('错误', route_url('demo:api.mgr_app.grid_request', ['error'], ['id' => data_get($row, 'id')]))->icon('Close');
            $actions->request('成功', route_url('demo:api.mgr_app.grid_request', ['success'], ['id' => data_get($row, 'id')]))->icon('Aim');
            $actions->request('Disabled', route_url('demo:api.mgr_app.grid_request', ['success'], ['id' => data_get($row, 'id')]))->icon('Apple')->disabled();
            $actions->request('Plain', route_url('demo:api.mgr_app.grid_request', ['success'], ['id' => data_get($row, 'id')]))->icon('Avatar')->plain();
            $actions->request('Primary', route_url('demo:api.mgr_app.grid_request', ['success'], ['id' => data_get($row, 'id')]))->icon('Bell')->primary();
            $actions->request('Plain', route_url('demo:api.mgr_app.grid_request', ['success'], ['id' => data_get($row, 'id')]))->icon('Burger')->primary()->plain();
            $actions->request('确认', route_url('demo:api.mgr_app.grid_request', ['success'], ['id' => data_get($row, 'id')]))->icon('Brush')->confirm();
            $actions->page('页面', route_url('demo:api.mgr_app.grid_form', ['detail']), 'form')->icon('Box');
        });
    }
}
