<?php

namespace Poppy\Area\Http\MgrPage;

use Closure;
use Poppy\Framework\Exceptions\ApplicationException;
use Poppy\MgrPage\Classes\Grid\Column;
use Poppy\MgrPage\Classes\Grid\Displayer\Actions;
use Poppy\MgrPage\Classes\Grid\Filter;
use Poppy\MgrPage\Classes\Grid\ListBase;
use Poppy\MgrPage\Classes\Grid\Tools\BaseButton;
use function route;
use function route_url;

class ListArea extends ListBase
{

    public $title = '地区管理';

    /**
     * @throws ApplicationException
     */
    public function columns()
    {
        $this->column('id', "ID")->sortable()->width(80);
        $this->column('title', "名称");
    }


    public function filter(): Closure
    {
        return function (Filter $filter) {
            $filter->column(1, function (Filter $column) {
                $column->like('title', '标题');
            });
            $filter->column(2, function (Filter $column) {
                $column->area('parent_id', '上级地区');
            });
        };
    }

    /**
     * @inheritDoc
     */
    public function actions()
    {
        $Action = $this;
        $this->addColumn(Column::NAME_ACTION, '操作')
            ->displayUsing(Actions::class, [
                function (Actions $actions) use ($Action) {
                    $item = $actions->row;
                    $actions->append([
                        $Action->edit($item),
                        $Action->delete($item),
                    ]);
                },
            ])->width(120);
    }


    public function quickButtons(): array
    {
        return [
            $this->create(),
            $this->fix(),
        ];
    }

    /**
     * 创建
     * @return BaseButton
     */
    public function create(): BaseButton
    {
        return new BaseButton('<i class="fa fa-plus"></i> 新增', route_url('py-area:backend.content.establish', null), [
            'title' => "新增",
            'class' => 'J_iframe layui-btn layui-btn-sm',
        ]);
    }

    /**
     * 更新
     * @return BaseButton
     */
    public function fix(): BaseButton
    {
        return new BaseButton('<i class="fa fa-plus"></i> 更新', route_url('py-area:backend.content.fix', null), [
            'title' => "更新",
            'class' => 'J_iframe layui-btn layui-btn-sm',
        ]);
    }

    /**
     * 编辑
     * @param $item
     * @return BaseButton
     */
    public function edit($item): BaseButton
    {
        return new BaseButton('<i class="fa fa-edit text-info"></i>', route('py-area:backend.content.establish', [$item->id]), [
            'title' => "编辑[{$item->title}]",
            'class' => 'J_iframe',
        ]);
    }

    /**
     * 删除
     * @param $item
     * @return BaseButton
     */
    public function delete($item): BaseButton
    {
        return new BaseButton('<i class="fa fa-times"></i>', route('py-area:backend.content.delete', [$item->id]), [
            'title' => "删除",
            'class' => 'text-danger J_request',
        ]);
    }
}
