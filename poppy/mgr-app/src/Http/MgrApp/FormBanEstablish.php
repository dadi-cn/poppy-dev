<?php

namespace Poppy\MgrApp\Http\Form;

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Route;
use Poppy\Framework\Classes\Resp;
use Poppy\Framework\Validation\Rule;
use Poppy\MgrApp\Classes\Grid\Filter\Query\Scope;
use Poppy\MgrApp\Classes\Widgets\FormWidget;
use Poppy\System\Action\Ban;
use Poppy\System\Models\PamBan;

class FormBanEstablish extends FormWidget
{
    protected string $title = '新增';

    private $id;

    public function __construct()
    {
        parent::__construct();
        $this->id = Route::input('id');
    }


    public function handle()
    {
        $scope                 = input(Scope::QUERY_NAME);
        $input                 = input();
        $input['account_type'] = $scope;

        $Ban = new Ban();
        if (!$Ban->establish($input)) {
            return Resp::error($Ban->getError());
        }

        return Resp::success('操作成功', 'motion|grid:reload');
    }

    public function data(): array
    {
        if ($this->id) {
            $pam = PamBan::findOrFail($this->id);
            return Arr::pluck($pam, ['type', 'value', 'note']);
        }
        return [];
    }

    public function form()
    {
        $this->select('type', '类型')->options(PamBan::kvType())->rules([
            Rule::required()
        ]);
        $this->text('value', '限制值')->rules([
            Rule::required()
        ])->help("如果是Ip支持如下几种格式 :  固定IP(192.168.1.1) ; IP段 : (192.168.1.1-192.168.1.21);  IP 掩码(192.168.1.1/24); IP 通配符(192.168.1.*)");
        $this->text('note', '备注');
    }
}
