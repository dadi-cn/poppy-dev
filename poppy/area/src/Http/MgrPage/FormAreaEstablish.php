<?php

namespace Poppy\Area\Http\MgrPage;

use Poppy\Area\Action\Area;
use Poppy\Area\Models\SysArea;
use Poppy\Framework\Classes\Resp;
use Poppy\Framework\Exceptions\ApplicationException;
use Poppy\Framework\Validation\Rule;
use Poppy\MgrPage\Classes\Widgets\FormWidget;
use Poppy\System\Classes\Traits\PamTrait;
use function input;
use function is_post;

class FormAreaEstablish extends FormWidget
{
    use PamTrait;

    public $ajax = true;

    private $id;

    /**
     * @var SysArea
     */
    private $item;

    /**
     * 设置id
     * @param $id
     * @return $this
     */
    public function setId($id): self
    {
        $this->id = $id;

        if ($id) {
            $this->item = SysArea::find($this->id);
            if (!$this->item) {
                throw new ApplicationException('无地区信息');
            }
        }
        return $this;
    }


    public function handle()
    {
        $id   = input('id');
        $Area = (new Area())->setPam($this->pam);
        if (is_post()) {
            if ($Area->establish(input(), $id)) {
                return Resp::success('添加版本成功', '_top_reload|1');
            }

            return Resp::error($Area->getError());
        }

        $id && $Area->initArea($id) && $Area->share();

    }

    public function data(): array
    {
        if ($this->item) {
            return [
                'id'    => $this->item->id,
                'title' => $this->item->title,
            ];
        }
        return [];
    }

    public function form()
    {
        if ($this->id) {
            $this->hidden('id', 'ID');
        }
        $this->text('title', '地区名称')->rules([
            Rule::nullable(),
        ]);
        $this->area('parent_id', '选择省级')->rules([
            Rule::nullable(),
        ]);
    }
}
