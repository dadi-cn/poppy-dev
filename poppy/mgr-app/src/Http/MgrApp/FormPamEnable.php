<?php

namespace Poppy\MgrApp\Http\MgrApp;

use Illuminate\Support\Facades\Route;
use Poppy\Framework\Classes\Resp;
use Poppy\Framework\Validation\Rule;
use Poppy\MgrApp\Classes\Widgets\FormWidget;
use Poppy\System\Action\Pam;
use Poppy\System\Models\PamAccount;

class FormPamEnable extends FormWidget
{

    protected string $title = '账号解禁';

    private int $id;

    /**
     * @var PamAccount
     */
    private PamAccount $pam;

    public function __construct()
    {
        parent::__construct();
        $this->id  = (int) Route::input('id');
        $this->pam = PamAccount::findOrFail($this->id);
    }


    public function handle()
    {
        $reason = input('reason', '');
        $id     = (int) Route::input('id');

        $Pam = (new Pam())->setPam(request()->user());
        if (!$Pam->enable($id, $reason)) {
            return Resp::error($Pam->getError());
        }

        return Resp::success('当前用户启用', 'motion|grid:reload');

    }

    public function data(): array
    {
        return [
            'id'   => $this->pam->id,
            'date' => $this->pam->disable_end_at,
        ];
    }

    /**
     * Build a form here.
     */
    public function form()
    {
        $this->datetime('date', '解禁日期')->rules([
            Rule::required()
        ]);
        $this->textarea('reason', '解禁原因');
    }
}
