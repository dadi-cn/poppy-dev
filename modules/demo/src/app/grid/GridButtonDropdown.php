<?php

namespace Demo\App\Grid;

use Poppy\Framework\Exceptions\ApplicationException;
use Poppy\MgrApp\Classes\Grid\Column\Render\ActionsRender;
use Poppy\MgrApp\Classes\Widgets\TableWidget;
use Poppy\MgrApp\Http\Grid\GridBase;

/**
 * 按钮
 */
class GridButtonDropdown extends GridBase
{
    /**
     * @inheritDoc
     */
    public function table(TableWidget $table)
    {
        $table->add('id');
        $table->action(function (ActionsRender $actions) {
            $row = $actions->getRow();
            $actions->styleDropdown(3);
            $actions->request('错误', route_url('demo:api.mgr_app.grid_request', ['error'], ['id' => data_get($row, 'id')]));
            $actions->request('成功', route_url('demo:api.mgr_app.grid_request', ['success'], ['id' => data_get($row, 'id')]));
            $actions->request('Disabled', route_url('demo:api.mgr_app.grid_request', ['success'], ['id' => data_get($row, 'id')]))->disabled();
            $actions->request('Plain', route_url('demo:api.mgr_app.grid_request', ['success'], ['id' => data_get($row, 'id')]))->plain();
            $actions->request('Primary', route_url('demo:api.mgr_app.grid_request', ['success'], ['id' => data_get($row, 'id')]))->primary();
            $actions->request('Plain', route_url('demo:api.mgr_app.grid_request', ['success'], ['id' => data_get($row, 'id')]))->primary()->plain();
            $actions->request('确认', route_url('demo:api.mgr_app.grid_request', ['success'], ['id' => data_get($row, 'id')]))->confirm();
            $actions->page('页面', route_url('demo:api.mgr_app.grid_form', ['detail']), 'form');
        });
    }
}
