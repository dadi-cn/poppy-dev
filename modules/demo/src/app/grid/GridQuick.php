<?php


namespace Demo\App\Grid;

use Demo\Models\DemoWebapp;
use Poppy\Framework\Exceptions\ApplicationException;
use Poppy\MgrApp\Classes\Widgets\TableWidget;
use Poppy\MgrApp\Http\Grid\GridBase;

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
        $table->add('id', 'QuickId')->quickId();
        $table->add('title', 'QuickTitle')->quickTitle();
        $table->add('title-large', 'QuickTitleLarge')->display(function () {
            return $this->title;
        })->quickTitle(true);
        $table->add('color', '显示 Html 样式')->quickTitle(true)->html(function () {
            return "<div style='{$this->style}'>$this->title</div>";
        });
        $table->add('link', '链接')->link()->ellipsis();
        $table->add('pdf', 'Pdf')->download();
        $table->add('post_at', 'QuickDatetime')->quickDatetime();
    }
}
