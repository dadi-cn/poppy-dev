<?php

namespace Poppy\MgrApp\Http\Grid;

use Closure;
use Poppy\Framework\Exceptions\ApplicationException;
use Poppy\MgrApp\Classes\Grid\Column\Render\ActionsRender;
use Poppy\MgrApp\Classes\Grid\Tools\Actions;
use Poppy\MgrApp\Classes\Widgets\FilterWidget;
use Poppy\System\Models\PamAccount;
use Poppy\System\Models\PamRole;

class GridPamRole extends GridBase
{

    public string $title = '角色管理';

    /**
     * @inheritDoc
     * @throws ApplicationException
     */
    public function columns()
    {
        $pam = $this->pam;
        $this->column('id', "ID")->sortable()->width(80);
        $this->column('title', "名称");
        $this->column('action', '操作')->displayUsing(ActionsRender::class, [function (ActionsRender $actions) use ($pam) {
            $row = $actions->getRow();
            $actions->default(['plain', 'circle', 'only']);
            $title = data_get($row, 'title');
            if ($pam->can('permission', $row)) {
                $actions->page('编辑权限', route('py-mgr-app:api-backend.role.menu', [data_get($row, 'id')]), 'form')->icon('Key');
            }
            if ($pam->can('edit', $row)) {
                $actions->page('编辑', route('py-mgr-app:api-backend.role.establish', [data_get($row, 'id')]), 'form')->icon('Edit');
            }

            if ($pam->can('delete', $row)) {
                $actions->request('删除', route('py-mgr-app:api-backend.role.delete', [data_get($row, 'id')]))->icon('Close')->danger()
                    ->confirm("确认删除角色 `{$title}`?");
            }
        }])->width(150, true)->fixed();
    }

    public function filter(FilterWidget $filter)
    {
        $types = PamAccount::kvType();
        foreach ($types as $t => $v) {
            $filter->scope($t, $v)->where('type', $t);
        }
    }


    public function quickActions(Actions $actions)
    {
        $pam = $this->pam;
        if ($pam->can('create', PamRole::class)) {
            $actions->page('新增', route_url('py-mgr-app:api-backend.role.establish'), 'form')->icon('CirclePlus');
        }

    }
}
