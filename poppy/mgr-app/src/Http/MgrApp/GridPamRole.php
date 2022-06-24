<?php

namespace Poppy\MgrApp\Http\MgrApp;

use Poppy\MgrApp\Classes\Filter\FilterPlugin;
use Poppy\MgrApp\Classes\Grid\GridBase;
use Poppy\MgrApp\Classes\Grid\Tools\Interactions;
use Poppy\MgrApp\Classes\Table\Render\GridActions;
use Poppy\MgrApp\Classes\Table\TablePlugin;
use Poppy\System\Models\PamAccount;
use Poppy\System\Models\PamRole;

class GridPamRole extends GridBase
{

    public string $title = '角色管理';

    /**
     * @inheritDoc
     */
    public function table(TablePlugin $table)
    {
        $pam = $this->pam;
        $table->add('id', "ID")->sortable()->width(80);
        $table->add('title', "名称");
        $table->add('handle', '操作')->asAction(function (GridActions $actions) use ($pam) {
            $row = $actions->getRow();
            $actions->default(['plain', 'circle', 'only']);
            $title = data_get($row, 'title');
            if ($pam->can('permission', $row)) {
                $actions->page('编辑权限', route('py-mgr-app:api.role.menu', [data_get($row, 'id')]), 'form')->icon('Key');
            }
            if ($pam->can('edit', $row)) {
                $actions->page('编辑', route('py-mgr-app:api.role.establish', [data_get($row, 'id')]), 'form')->icon('Edit');
            }

            if ($pam->can('delete', $row)) {
                $actions->request('删除', route('py-mgr-app:api.role.delete', [data_get($row, 'id')]))->icon('Close')->danger()
                    ->confirm("确认删除角色 `{$title}`?");
            }
        })->width(150, true)->fixed();
    }

    /**
     * @param FilterPlugin $filter
     * @return void
     */
    public function filter(FilterPlugin $filter)
    {
        $types = PamAccount::kvType();
        $filter->scope('all', '全部');
        foreach ($types as $t => $v) {
            $filter->scope($t, $v)->where('type', $t);
        }
    }


    public function quick(Interactions $actions)
    {
        $pam = $this->pam;
        if ($pam->can('create', PamRole::class)) {
            $actions->page('新增', route_url('py-mgr-app:api.role.establish'), 'form')->icon('CirclePlus');
        }

    }
}
