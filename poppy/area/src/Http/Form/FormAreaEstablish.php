<?php

namespace Poppy\Area\Http\Form;

use Auth;
use Illuminate\Support\Facades\Route;
use Poppy\Area\Action\Area;
use Poppy\Area\Models\SysArea;
use Poppy\Framework\Classes\Resp;
use Poppy\Framework\Validation\Rule;
use Poppy\MgrApp\Classes\Widgets\FormWidget;
use Poppy\System\Classes\Traits\PamTrait;
use Poppy\System\Models\PamAccount;

class FormAreaEstablish extends FormWidget
{
    use PamTrait;

    protected string $title = '新建地域';

    private int $id;

    /**
     * @var ?SysArea
     */
    private ?SysArea $item = null;

    public function __construct()
    {
        parent::__construct();
        $this->id = (int) Route::input('id');
        if ($this->id) {
            $this->title = '编辑地域';
            $this->item  = SysArea::findOrFail($this->id);
        }
    }


    public function handle()
    {
        /** @var PamAccount $pam */
        $pam  = Auth::user();
        $Area = (new Area())->setPam($pam);
        if ($Area->establish(input(), $this->id)) {
            return Resp::success('处理成功', 'motion|grid:reload');
        }

        return Resp::error($Area->getError());
    }

    public function data(): array
    {
        if ($this->item) {
            return [
                'parent_id' => $this->item->parent_id ? (string) $this->item->parent_id : null,
                'title'     => $this->item->title,
            ];
        }
        return [];
    }

    public function form()
    {
        $this->text('title', '地区名称')->rules([
            Rule::nullable(),
        ]);
        $this->select('parent_id', '选择省级')->rules([
            Rule::nullable(),
        ])->options(SysArea::cityMgrTree())->filterable()->placeholder('选择上一级');
    }
}
