<?php

namespace Poppy\SensitiveWord\Http\MgrPage;

use Closure;
use Poppy\Framework\Exceptions\ApplicationException;
use Poppy\MgrPage\Classes\Grid\Column;
use Poppy\MgrPage\Classes\Grid\Displayer\Actions;
use Poppy\MgrPage\Classes\Grid\Filter;
use Poppy\MgrPage\Classes\Grid\ListBase;
use Poppy\MgrPage\Classes\Grid\Tools\ActionButton;
use Poppy\MgrPage\Classes\Grid\Tools\BaseButton;
use function route;
use function route_url;

class ListSensitiveWord extends ListBase
{

    public $title = '敏感词';

    /**
     * @throws ApplicationException
     */
    public function columns()
    {
        $this->column('id', "ID")->sortable()->width(80);
        $this->column('word', "敏感词");
    }


    public function filter(): Closure
    {
        return function (Filter $filter) {
            $filter->column(1 / 12, function (Filter $column) {
                $column->like('word', '敏感词');
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
                        $Action->delete($item),
                    ]);
                },
            ])->width(120);
    }

    public function batchAction(): array
    {
        return [
            new ActionButton('<i class="fa fa-trash"></i> 删除', route_url('py-sensitive-word:backend.word.delete', null), [
                'title'        => "删除",
                'data-confirm' => "确认删除选中数据",
                'class'        => 'layui-btn layui-btn-sm layui-btn-danger',
            ]),
        ];
    }

    public function quickButtons(): array
    {
        return [
            new BaseButton('<i class="fa fa-plus"></i> 新增', route_url('py-sensitive-word:backend.word.establish', null), [
                'title' => "新增",
                'class' => 'J_iframe layui-btn layui-btn-sm',
            ]),
        ];
    }

    /**
     * 删除
     * @param $item
     * @return BaseButton
     */
    public function delete($item): BaseButton
    {
        return new BaseButton('<i class="fa fa-times"></i>', route('py-sensitive-word:backend.word.delete', [$item->id]), [
            'title' => "删除",
            'class' => 'text-danger J_request',
        ]);
    }
}
