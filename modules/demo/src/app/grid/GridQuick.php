<?php


namespace Demo\App\Grid;

use Demo\Models\DemoWebapp;
use Poppy\MgrApp\Classes\Grid\GridBase;
use Poppy\MgrApp\Classes\Widgets\TableWidget;

/**
 * 快捷列表
 * @mixin DemoWebapp
 */
class GridQuick extends GridBase
{
    public string $title = '列快捷展示';

    /**
     * @inheritDoc
     */
    public function table(TableWidget $table)
    {
        $table->add('id', '定宽ID')->quickId();
        $table->add('title', '标题')->quickTitle();
        $table->add('title-large', '宽标题')->display(function () {
            return $this->title;
        })->quickTitle(true);
        $table->add('post_at', 'QuickDatetime')->quickDatetime();
    }
}
