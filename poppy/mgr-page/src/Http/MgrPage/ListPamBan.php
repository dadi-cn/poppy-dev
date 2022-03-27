<?php

namespace Poppy\MgrPage\Http\MgrPage;

use Closure;
use Poppy\Framework\Exceptions\ApplicationException;
use Poppy\MgrPage\Classes\Grid\Column;
use Poppy\MgrPage\Classes\Grid\Displayer\Actions;
use Poppy\MgrPage\Classes\Grid\Filter;
use Poppy\MgrPage\Classes\Grid\ListBase;
use Poppy\MgrPage\Classes\Grid\Tools\BaseButton;
use Poppy\System\Models\PamAccount;
use Poppy\System\Models\PamBan;
use Poppy\System\Models\SysConfig;
use function input;
use function route;
use function route_url;
use function sys_setting;

class ListPamBan extends ListBase
{

    public $title = '风险拦截';

    /**
     * @inheritDoc
     * @throws ApplicationException
     */
    public function columns()
    {
        $this->column('id', "ID")->sortable()->width(80);
        $this->column('type', "类型")->display(function ($type) {
            return PamBan::kvType($type);
        });
        $this->column('value', "限制值");
    }


    /**
     * @inheritDoc
     * @return Closure
     */
    public function filter(): Closure
    {
        return function (Filter $filter) {
            $types = PamAccount::kvType();
            foreach ($types as $t => $v) {
                $filter->scope($t, $v)->where('account_type', $t);
            }
        };
    }

    /**
     * @inheritDoc
     */
    public function actions()
    {
        $this->addColumn(Column::NAME_ACTION, '操作')
            ->displayUsing(Actions::class, [
                function (Actions $actions) {
                    /** @var PamBan $item */
                    $item = $actions->row;
                    $actions->append([
                        new BaseButton('<i class="fa fa-times"></i>', route('py-mgr-page:backend.ban.delete', [$item->id]), [
                            'title' => "删除",
                            'class' => 'text-danger J_request',
                        ]),
                    ]);
                },
            ]);
    }


    public function quickButtons(): array
    {
        $type = input(\Poppy\MgrPage\Classes\Grid\Filter\Scope::QUERY_NAME, PamAccount::TYPE_USER);

        // 黑名单/白名单
        $status  = sys_setting('py-mgr-page::ban.status-' . $type, SysConfig::DISABLE);
        $isBlack = sys_setting('py-mgr-page::ban.type-' . $type, PamBan::WB_TYPE_BLACK) === PamBan::WB_TYPE_BLACK;
        return [
            new BaseButton($status ? '<i class="fa fa-toggle-on"></i> 已启用' : '<i class="fa fa-toggle-off"></i> 已禁用',
                route_url('py-mgr-page:backend.ban.status', null, ['type' => $type,]), [
                    'title' => $status ? '当前启用, 点击禁用' : '当前禁用, 点击启用',
                    'class' => 'J_request layui-btn layui-btn-sm ' . ($status ? 'layui-btn-normal' : 'layui-btn-danger'),
                ]),
            new BaseButton($isBlack ? '<i class="fa fa-ban"></i> 黑名单模式' : '<i class="fa fa-filter"></i> 白名单模式',
                route_url('py-mgr-page:backend.ban.type', null, ['type' => $type,]), [
                    'title'        => $isBlack ? '当前黑名单, 点击切换到白名单' : '当前白名单, 点击切换到黑名单',
                    'data-confirm' => $isBlack ? '当前黑名单, 是否切换到白名单?' : '当前白名单, 是否切换到黑名单?',
                    'class'        => 'J_request layui-btn layui-btn-sm ' . ($isBlack ? 'layui-btn-danger' : 'layui-btn-normal'),
                ]),
            new BaseButton('<i class="fa fa-plus"></i> 新增',
                route_url('py-mgr-page:backend.ban.establish', null, ['type' => $type,]), [
                    'title' => "新增",
                    'class' => 'J_iframe layui-btn layui-btn-sm',
                ]),
        ];
    }
}
