<?php

namespace Poppy\MgrApp\Http\Form;

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Route;
use Poppy\Framework\Classes\Resp;
use Poppy\Framework\Exceptions\ApplicationException;
use Poppy\MgrApp\Classes\Widgets\FormWidget;
use Poppy\System\Action\Role;
use Poppy\System\Models\PamRole;

class FormRolePermission extends FormWidget
{

    protected string $title = '编辑权限';

    private $id;

    private $item;

    private $permission;

    /**
     * @throws ApplicationException
     */
    public function __construct()
    {
        parent::__construct();
        $this->id   = Route::input('id');
        $this->item = PamRole::findOrFail($this->id);
        $permission = (new Role())->permissions($this->id);
        if (!$permission) {
            throw new ApplicationException('暂无权限信息(请检查是否初始化权限)！');
        }
        $this->permission = $permission;
    }


    public function handle()
    {
        if (is_post()) {
            $input = array_filter(Arr::flatten(Arr::except(input(), ['sign', 'timestamp'])));
            $Role  = (new Role())->setPam(request()->user());
            if (!$Role->savePermission($this->id, array_values($input))) {
                return Resp::success($Role->getError());
            }

            return Resp::success('保存会员权限配置成功!');
        }
    }

    public function data(): array
    {
        $data = [];
        foreach ($this->permission as $key => $permission) {
            foreach ($permission['groups'] as $group) {
                $sv = [];
                collect($group['permissions'])->each(function ($item) use (&$sv) {
                    if ($item['value']) {
                        $sv[] = $item['id'];
                    }
                });
                $data[$key . '-' . $group['group']] = $sv;
            }
        }
        return $data;
    }

    public function form()
    {
        foreach ($this->permission as $key => $permission) {
            $this->divider($permission['title']);
            foreach ($permission['groups'] as $group) {
                $singlePerms = collect($group['permissions'])->map(function ($item) {
                    return [
                        'label' => $item['description'],
                        'value' => $item['id'],
                    ];
                });
                $this->checkbox($key . '-' . $group['group'], $group['title'])->options($singlePerms->toArray());
            }
        }
    }
}
