<?php


namespace Demo\App\Grid;

use Demo\Models\DemoWebapp;
use Poppy\MgrApp\Classes\Grid\GridBase;
use Poppy\MgrApp\Classes\Table\TablePlugin;

/**
 * @mixin DemoWebapp
 */
class GridEditAsText extends GridBase
{
    public string $title = '快捷编辑';

    /**
     * @inheritDoc
     */
    public function table(TablePlugin $table)
    {
        $table->add('id', 'ID')->quickId();
        $table->add('sort', '行内编辑')->copyable()->asInlineSaveText();
        $table->add('sort-alt', '行内编辑(字段更名)')->copyable()->asInlineSaveText(function () {
            return $this->sort;
        })->query('sort');
        $table->add('sort-disable', '行内编辑(禁用部分)')->copyable()->asInlineSaveText(function () {
            return $this->sort;
        }, function () {
            return $this->id % 3 === 0;
        })->query('sort');
        $table->add('sort-error', '行内编辑(请求错误)')->copyable()->asInlineSaveText(function () {
            return $this->sort;
        })->query('sort', route('demo:api.mgr_app.grid_request', ['error']));
        $table->add('sort-error', '输入状态')->copyable()->asModifyText(function () {
            return $this->sort;
        });
    }
}
