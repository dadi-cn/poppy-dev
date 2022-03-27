<?php

namespace Poppy\MgrPage\Http\MgrPage;

use Poppy\Framework\Classes\Resp;
use Poppy\Framework\Exceptions\ApplicationException;
use Poppy\Framework\Validation\Rule;
use Poppy\MgrPage\Classes\Widgets\FormWidget;
use Poppy\System\Action\Ban;
use Poppy\System\Classes\Traits\PamTrait;
use Poppy\System\Models\PamBan;
use function input;

class FormBanEstablish extends FormWidget
{

    use PamTrait;

    public $ajax = true;


    private $id;

    /**
     * @var PamBan
     */
    private $item;

    /**
     * 账号类型
     * @var string
     */
    private $accountType;

    public function setAccountType($type)
    {
        $this->accountType = $type;
    }

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
            $this->item = PamBan::find($id);

            if (!$this->item) {
                throw new ApplicationException('无设备信息');
            }
        }
        return $this;
    }

    public function handle()
    {
        $Ban = new Ban();
        if (!$Ban->establish(input())) {
            return Resp::error($Ban->getError());
        }

        return Resp::success('操作成功', '_top_reload|1');
    }

    public function data(): array
    {
        $data = [];
        if ($this->id) {
            return [
                'id'           => $this->item->id,
                'account_type' => $this->item->account_type,
                'type'         => $this->item->type,
                'value'        => $this->item->value,
            ];
        }
        else {
            return array_merge($data, [
                'account_type' => $this->accountType,
            ]);
        }
    }

    public function form()
    {
        if ($this->id) {
            $this->hidden('id', '设备id');
        }
        $this->hidden('account_type', '账号类型');
        $this->select('type', '类型')->options(PamBan::kvType());
        $this->text('value', '限制值')->rules([
            Rule::nullable(),
        ])->help('如果是Ip支持如下几种格式 : <br> 固定IP(192.168.1.1) ; IP段 : (192.168.1.1-192.168.1.21); <br> IP 掩码(192.168.1.1/24); IP 通配符(192.168.1.*)');
    }
}
