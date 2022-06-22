<?php


namespace Demo\App\Grid;

use Demo\Models\DemoWebapp;
use Poppy\MgrApp\Classes\Grid\GridBase;
use Poppy\MgrApp\Classes\Widgets\TableWidget;

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
        $table->add('status', 'usingKv')->asKv(DemoWebapp::kvStatus())->width(100, true);
        $table->add('title-large', 'display(自定义组合数据)')->display(function () {
            return $this->title . '|' . $this->id;
        })->quickTitle(true);
        $table->add('color', 'Html')->asHtml(function () {
            return "<div style='{$this->style}'>$this->title</div>";
        })->quickTitle(true);
        $table->add('link', '链接')->asLink()->ellipsis();
        $table->add('image', '图片')->asImage();
        $table->add('images', '多图')->asImage();
        $table->add('pdf', 'Pdf')->asDownload();
        $table->add('loading', 'Loading')->asOnOff();
    }
}
