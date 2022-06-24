<?php

namespace Demo\App\Forms;

use Poppy\Framework\Classes\Resp;
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
            'id'    => 5,
            'table' => [
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
            $table->add('id', 'ID');
            $table->add('name', '用户名');
        });
    }
}
