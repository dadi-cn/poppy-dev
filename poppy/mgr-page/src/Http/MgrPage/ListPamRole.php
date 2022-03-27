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
use Poppy\System\Models\PamRole;
use function route;

class ListPamRole extends ListBase
{

    public $title = '角色管理';

    /**
     * @inheritDoc
     * @throws ApplicationException
     */
    public function columns()
    {
        $this->column('id', "ID")->sortable()->width(80);
        $this->column('title', "名称");
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
                $filter->scope($t, $v)->where('type', $t);
            }
        };
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
                        $Action->permission($item),
                        $Action->edit($item),
                        $Action->delete($item),
                    ]);
                },
            ]);
    }


    public function quickButtons(): array
    {
        return [
            $this->create(),
        ];
    }


    /**
     * 创建
     * @return BaseButton
     */
    public function create(): ?BaseButton
    {
        if ($this->pam->can('create', PamRole::class)) {
            return new BaseButton('<i class="fa fa-plus"></i> 新增', route('py-mgr-page:backend.role.establish'), [
                'class' => 'layui-btn layui-btn-sm J_iframe',
                'title' => '新增',
            ]);
        }
        return null;
    }

    /**
     * 修改密码
     * @param $item
     * @return BaseButton
     */
    public function permission($item): ?BaseButton
    {
        if ($this->pam->can('permission', $item)) {
            return new BaseButton('<i class="fa fa-user-check"></i>', route('py-mgr-page:backend.role.menu', [$item->id]), [
                'class' => 'J_iframe ',
                'title' => "编辑 [{$item->title}] 权限",
            ]);
        }
        return null;
    }


    /**
     * 编辑
     * @param $item
     * @return BaseButton
     */
    public function edit($item): ?BaseButton
    {
        if ($this->pam->can('edit', $item)) {
            return new BaseButton('<i class="fa fa-edit"></i>', route('py-mgr-page:backend.role.establish', [$item->id]), [
                'title' => "编辑 [{$item->title}] ",
                'class' => "J_iframe",
            ]);
        }
        return null;
    }

    /**
     * 删除
     * @param $item
     * @return BaseButton|null
     */
    public function delete($item): ?BaseButton
    {
        if ($this->pam->can('delete', $item)) {
            return new BaseButton('<i class="fa fa-times"></i>', route('py-mgr-page:backend.role.delete', [$item->id]), [
                'title'        => "删除角色 [{$item->title}] ",
                'data-confirm' => "确认删除角色 `{!! $item->title !!}`?",
                'class'        => 'text-danger J_request',
            ]);
        }
        return null;
    }
}
