<?php

namespace Demo\App\Forms;

use Poppy\Framework\Classes\Resp;
use Poppy\MgrApp\Classes\Widgets\FormWidget;

class FormFieldButtonEstablish extends FormWidget
{
    protected string $title = '禁用 Reset 按钮';

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
            'country' => 'HK',
        ];
    }

    public function form()
    {
        $this->select('country', '地区')->depend('area', 'type|country')->filterable();
        $this->disableReset();
    }
}
