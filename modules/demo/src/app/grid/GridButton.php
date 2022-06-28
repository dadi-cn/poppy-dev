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
            $actions->request('成功', route('demo:api.grid.request', ['success']))->success();
            $actions->request('错误', route('demo:api.grid.request', ['error']))->danger();
            $actions->request('确认', route('demo:api.grid.request', ['success']))->info()->confirm();
            $actions->copy('复制', '可以复制的内容')->icon('mu:content_copy')->circle()->only();
            $actions->page('Page & Form', route('demo:api.form.auto', ['field-text']), 'form');
            $actions->dialog('Dialog & Form', route('demo:api.form.auto', ['field-text']), 'form');
            $actions->page('Page & Grid', route('demo:api.grid.auto', ['layout']), 'grid');
            $actions->target('Target(百度)', 'https://www.baidu.com');
            $actions->iframe('Iframe', 'https://poppy-framework.com/');
            $actions->page('Table', route('demo:api.table.index', ['simple']), 'table');
        });
    }

}
