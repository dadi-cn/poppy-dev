<?php

namespace Poppy\MgrApp\Http\Form;

use Illuminate\Support\Facades\Route;
use Poppy\Framework\Classes\Resp;
use Poppy\Framework\Validation\Rule;
use Poppy\MgrApp\Classes\Widgets\FormWidget;
use Poppy\System\Action\Role;
use Poppy\System\Models\PamAccount;
use Poppy\System\Models\PamRole;

class FormRoleEstablish extends FormWidget
{


    private $id;

    public function __construct()
    {
        parent::__construct();
        $this->id = Route::input('id');
    }


    public function handle()
    {
        $Role = (new Role());
        $Role->setPam(request()->user());
        if ($Role->establish(input(), $this->id)) {
            return Resp::success('操作成功', 'motion|grid:reload');
        }

        return Resp::error($Role->getError());
    }

    public function data(): array
    {
        if ($this->id) {
            $item = PamRole::findOrFail($this->id);
            return [
                'title' => $item->title,
                'name'  => $item->name,
                'type'  => $item->type,
            ];
        }
        return [];
    }

    public function form()
    {
        if ($this->id) {
            $this->select('type', '角色组')->options(PamAccount::kvType())->disabled();
        } else {
            $this->select('type', '角色组')->options(PamAccount::kvType())->rules([
                Rule::required(),
            ]);
        }
        $this->text('name', '标识')->help('角色标识在后台不进行显示, 如果需要进行项目内部约定');
        $this->text('title', '角色名称')->rules([
            Rule::required(),
        ])->help('显示的名称');
    }
}
