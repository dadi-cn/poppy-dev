<?php

namespace Poppy\MgrPage\Http\MgrPage;

use Poppy\Framework\Classes\Resp;
use Poppy\Framework\Exceptions\ApplicationException;
use Poppy\MgrPage\Classes\Widgets\FormWidget;
use Poppy\System\Action\Pam;
use Poppy\System\Models\PamAccount;
use function input;
use function request;

class FormPamEnable extends FormWidget
{
    public $ajax = true;

    private $id;

    /**
     * @var PamAccount
     */
    private $pam;

    /**
     * @param $id
     * @return $this
     * @throws ApplicationException
     */
    public function setId($id): self
    {
        $this->id = $id;
        if ($id) {
            $this->pam = PamAccount::find($this->id);

            if (!$this->pam) {
                throw  new ApplicationException('无用户数据');
            }

        }
        return $this;
    }

    public function handle()
    {
        if (!$this->id) {
            return Resp::error('您尚未选择用户!');
        }

        $Pam    = (new Pam())->setPam(request()->user());
        $reason = input('reason', '');
        if (!$Pam->enable($this->id, $reason)) {
            return Resp::error($Pam->getError());
        }

        return Resp::success('当前用户启用', '_top_reload|1');

    }

    public function data(): array
    {
        if ($this->id) {
            return [
                'id'   => $this->pam->id,
                'date' => $this->pam->disable_end_at,
            ];
        }
        return [];
    }

    /**
     * Build a form here.
     */
    public function form()
    {
        if ($this->id) {
            $this->hidden('id', 'ID');
        }
        $this->datetime('date', '解禁日期')->disable();
        $this->textarea('reason', '解禁原因');
    }
}
