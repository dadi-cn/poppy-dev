<?php

namespace Demo\App\Dashboard;

use Poppy\Framework\Classes\Resp;
use Poppy\Framework\Validation\Rule;
use Poppy\MgrApp\Classes\Dashboard\PanelForm;
use Poppy\MgrApp\Classes\Form\FormPlugin;
use Poppy\MgrApp\Classes\Widgets\DashboardWidget;

/**
 * 没有范围
 */
class DashboardNoScope extends DashboardWidget
{
    public string $title = '仪表盘';

    public function __construct()
    {
        parent::__construct();
    }

    public function panels(): array
    {
        $form1 = (new PanelForm('site', 12))->form(function (FormPlugin $form) {
            $form->text('Title', '标题')->rules([
                Rule::required(),
            ]);
        })->handle(function ($data) {
            return Resp::success('返回成功' . var_export($data, true));
        });
        return [$form1];
    }
}
