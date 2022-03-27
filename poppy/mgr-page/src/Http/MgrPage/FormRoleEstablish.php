<?php

namespace Poppy\MgrPage\Http\MgrPage;

use Poppy\Framework\Classes\Resp;
use Poppy\Framework\Exceptions\ApplicationException;
use Poppy\Framework\Validation\Rule;
use Poppy\MgrPage\Classes\Widgets\FormWidget;
use Poppy\System\Action\Role;
use Poppy\System\Models\PamAccount;
use Poppy\System\Models\PamRole;
use function is_post;
use function request;

class FormRoleEstablish extends FormWidget
{

    public $ajax = true;


    private $id;

    /**
     * @var PamRole
     */
    private $item;

    /**
     * 设置id
     * @param $id
     * @return $this
     * @throws ApplicationException
     */
    public function setId($id)
    {
        $this->id = $id;
        if ($id) {
            $this->item = PamRole::find($id);

            if (!$this->item) {
                throw  new ApplicationException('无用户数据');
            }
        }
        return $this;
    }

    public function handle()
    {
        $Role = (new Role());
        $Role->setPam(request()->user());
        if (is_post()) {
            if ($Role->establish(request()->all(), $this->id)) {
                return Resp::success('操作成功', '_top_reload|1;id|' . $Role->getRole()->id);
            }

            return Resp::error($Role->getError());
        }
        $this->id && $Role->init($this->id) && $Role->share();
    }

    public function data(): array
    {
        if ($this->id) {
            return [
                'title' => $this->item->title,
                'name'  => $this->item->name,
                'type'  => $this->item->type,
            ];
        }
        return [];
    }

    public function form()
    {
        if ($this->id) {
            $this->select('type', '角色组')->options(PamAccount::kvType())->attribute([
                'lay-ignore',
            ])->disable();
        }
        else {
            $this->select('type', '角色组')->options(PamAccount::kvType())->rules([
                Rule::required(),
            ])->attribute([
                'lay-ignore',
            ]);
        }
        $this->text('name', '标识')->help('角色标识在后台不进行显示, 如果需要进行项目内部约定');
        $this->text('title', '角色名称')->rules([
            Rule::required(),
        ])->help('显示的名称');
    }
}
