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
class GridButtonStyle extends GridBase
{
    /**
     * @inheritDoc
     */
    public function table(TablePlugin $table)
    {
        $table->add('id')->quickId();
        $table->add('handle', '操作')->asAction(function (GridActions $actions) {
            $actions->request('Plain', route('demo:api.grid.request', ['success']))->plain();
            $actions->request('Primary', route('demo:api.grid.request', ['error']))->primary();
            $actions->request('Danger', route('demo:api.grid.request', ['success']))->danger();
            $actions->request('Danger&Disabled', route('demo:api.grid.request', ['success']))->danger()->disabled();
            $actions->request('Info', route('demo:api.grid.request', ['success']))->info();
            $actions->request('Info&Plain', route('demo:api.grid.request', ['success']))->plain()->info();
            $actions->request('Success', route('demo:api.grid.request', ['success']))->success();
            $actions->request('Primary Plain', route('demo:api.grid.request', ['success']))->primary()->plain();
            $actions->request('Icon', route('demo:api.grid.request', ['success']))->icon('warning');
            $actions->request('Icon&Only', route('demo:api.grid.request', ['success']))->icon('warning')->only();
            $actions->request('Material Ui', route('demo:api.grid.request', ['success']))->icon('mu:view_kanban');
            $actions->request('Material Ui Icon', route('demo:api.grid.request', ['success']))->icon('mu:view_kanban')->only();
            $actions->request('Material Ui Icon', route('demo:api.grid.request', ['success']))->icon('mu:view_kanban')->only()->circle();
            $actions->request('Icon&Circle', route('demo:api.grid.request', ['success']))->icon('warning')->circle()->only();
        });
    }

}
