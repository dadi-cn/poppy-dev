<?php


namespace Demo\App\Grid;

use Demo\Models\DemoWebapp;
use Poppy\MgrApp\Classes\Grid\Column\Render\ActionsRender;
use Poppy\MgrApp\Classes\Widgets\TableWidget;
use Poppy\MgrApp\Http\Grid\GridBase;

/**
 * 快捷列表
 * @mixin DemoWebapp
 */
class GridDisplay extends GridBase
{
    public string $title = '列快捷展示';

    /**
     * @inheritDoc
     */
    public function table(TableWidget $table)
    {
        $table->add('id', 'QuickId')->quickId();
        $table->add('status', 'Status')->usingKv(DemoWebapp::kvStatus());
        $table->add('title-large', 'QuickTitleLarge')->display(function () {
            return $this->title;
        })->quickTitle(true);
        $table->add('color', 'QuickTitleLarge')->html(function () {
            return "<div style='{$this->style}'>$this->title</div>";
        })->quickTitle(true);
        $table->add('post_at', 'QuickDatetime')->quickDatetime();
        $table->action(function (ActionsRender $actions) {
            $actions->request('错误', route('demo:api.mgr_app.grid_request', ['error']));
        });
    }
}
