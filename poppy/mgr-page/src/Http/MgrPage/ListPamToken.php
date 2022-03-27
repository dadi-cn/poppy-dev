<?php

namespace Poppy\MgrPage\Http\MgrPage;

use Poppy\Framework\Exceptions\ApplicationException;
use Poppy\MgrPage\Classes\Grid\Column;
use Poppy\MgrPage\Classes\Grid\Displayer\Actions;
use Poppy\MgrPage\Classes\Grid\ListBase;
use Poppy\MgrPage\Classes\Grid\Tools\BaseButton;
use function route;

class ListPamToken extends ListBase
{

    public $title = '登录用户管理';

    /**
     * @inheritDoc
     * @throws ApplicationException
     */
    public function columns()
    {
        $this->column('id', "ID")->sortable()->width(80);
        $this->column('account_id', "用户ID");
        $this->column('device_type', "设备类型");
        $this->column('device_id', "设备ID");
        $this->column('login_ip', "登录IP");
        $this->column('expired_at', "过期时间");
    }


    /**
     * @inheritDoc
     */
    public function actions()
    {
        $Action = $this;
        $this->addColumn(Column::NAME_ACTION, '操作')
            ->displayUsing(Actions::class, [
                function (Actions $actions) use ($Action) {
                    $item = $actions->row;
                    $actions->append([
                        $Action->ban($item, 'ip'),
                        $Action->ban($item, 'device'),
                        $Action->delete($item),
                    ]);
                },
            ]);
    }


    /**
     * 修改密码
     * @param $item
     * @param $type
     * @return BaseButton
     */
    public function ban($item, $type): ?BaseButton
    {
        $desc = $type === 'ip' ? 'IP' : '设备';
        $icon = $type === 'ip' ? 'fa-unlink' : 'fa-mobile-alt';
        return new BaseButton('<i class="fa ' . $icon . '"></i>', route('py-mgr-page:backend.pam.ban', [$item->id, $type]), [
            'class' => 'J_request ',
            'title' => "禁用{$desc}",
        ]);
    }


    /**
     * 编辑
     * @param $item
     * @return BaseButton
     */
    public function delete($item): ?BaseButton
    {
        return new BaseButton('<i class="fa fa-close"></i>', route('py-mgr-page:backend.pam.delete_token', [$item->id]), [
            'title'        => "下线用户",
            'data-confirm' => "确认下线用户{$item->account_id} , 用户重新登录仍可访问? ",
            'class'        => "J_request",
        ]);
    }
}
