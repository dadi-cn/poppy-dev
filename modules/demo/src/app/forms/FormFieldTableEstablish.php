<?php

namespace Demo\App\Forms;

use Demo\Models\DemoWebapp;
use Poppy\Framework\Classes\Resp;
use Poppy\MgrApp\Classes\Table\EzTable;
use Poppy\MgrApp\Classes\Table\TablePlugin;
use Poppy\MgrApp\Classes\Widgets\FormWidget;

class FormFieldTableEstablish extends FormWidget
{

    public function handle()
    {
        $message = print_r(input(), true);
        return Resp::success($message);
    }

    /**
     */
    public function data(): array
    {
        return [
            'id'       => 5,
            'table'    => [
                [
                    'id'   => 1,
                    'name' => '多厘',
                ],
                [
                    'id'   => 2,
                    'name' => '晴天',
                ],
            ],
        ];
    }

    public function form()
    {
        $this->table('table', '表格')->cols(function (TablePlugin $table) {
            $table->add('id', 'ID')->asModifyText();
            $table->add('status', '状态')->asModifySelect()->options(DemoWebapp::kvStatus());
            $table->add('is_open', '开关')->asModifyOnOff();
            $table->add('name', '用户名');
            $table->add('action', '操作')->width(160, true)->asTableAction();
        });
        $this->ezTable('ez-table', '表格')->easy([
            ['Header', 'Title', 'Value', '图片'],
            ['网站', '地址', 'https://www.baidu.com', EzTable::image('https://jdc.jd.com/img/428x280?color=282828&textColor=EAE0D0&text=wulicode')],
            ['网站', '名称', '百度', EzTable::image('https://start.wulicode.com/img/428x280/wulicode')],
        ]);
    }
}
