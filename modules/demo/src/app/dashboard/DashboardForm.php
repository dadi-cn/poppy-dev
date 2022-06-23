<?php

namespace Demo\App\Dashboard;

use Poppy\Framework\Classes\Resp;
use Poppy\Framework\Validation\Rule;
use Poppy\MgrApp\Classes\Dashboard\PanelForm;
use Poppy\MgrApp\Classes\Form\FormPlugin;
use Poppy\MgrApp\Classes\Widgets\DashboardWidget;

/**
 * 表单
 */
class DashboardForm extends DashboardWidget
{
    public string $title = '仪表盘';

    public function __construct()
    {
        parent::__construct();
        $this->scope('test', 'TEST');
        $this->scope('mark', 'Mark');
    }


    public function handle(string $key = '', array $input = [])
    {
        return Resp::success('');
    }

    public function panels(): array
    {
        $scope = $this->getCurrentScope();
        $form1 = (new PanelForm('site', 12))->form(function (FormPlugin $form) use ($scope) {
            $form->text($scope->value . ':Title', $scope->label . '标题')->rules([
                Rule::required(),
            ]);
        })->handle(function ($data) {
            return Resp::success('返回成功' . var_export($data, true));
        });
        return [$form1];
    }
}
