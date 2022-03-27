<?php

namespace Demo\Http\Lists;

use Closure;
use Poppy\Framework\Exceptions\ApplicationException;
use Poppy\MgrPage\Classes\Grid\Column;
use Poppy\MgrPage\Classes\Grid\Displayer\Actions;
use Poppy\MgrPage\Classes\Grid\Filter;
use Poppy\MgrPage\Classes\Grid\ListBase;
use Poppy\MgrPage\Classes\Grid\Tools\BaseButton;

class ListPoppyEditable extends ListBase
{

    /**
     * @inheritDoc
     * @throws ApplicationException
     */
    public function columns()
    {
        $this->column('id', 'Id');
        $this->column('list_order', 'ListOrder')->editable()->sortable();

        $this->column('image', '图片(可放大)')->image('', 28, 28);
        $this->column('title', '标题(可复制)')->copyable();
        $this->column('progress')->progress();
        $this->column('loading')->loading(['N'], ['Y' => 'over']);
        $this->column('qr')->display(function () {
            return data_get($this, 'link');
        })->qrcode();

        $this->column('title-hide', '标题(隐藏)')->display(function () {
            return data_get($this, 'title');
        })->secret();
        $this->column('a')->label('danger');


    }


    /**
     * @inheritDoc
     * @return Closure
     */
    public function filter(): Closure
    {
        return function (Filter $filter) {
            $filter->column(1, function (Filter $filter) {
                $filter->like('username', 'username');
            });
            $filter->column(1, function (Filter $filter) {
                $filter->equal('status')->integer();
            });
            $filter->column(1, function (Filter $filter) {
                $filter->startsWith('title');
            });
            $filter->column(1, function (Filter $filter) {
                $filter->lt('progress')->integer();
            });
            $filter->column(1, function (Filter $filter) {
                $filter->gt('progress')->integer();
            });
            $filter->column(1, function (Filter $filter) {
                $filter->day('day');
            });
            $filter->column(1, function (Filter $filter) {
                $filter->date('date');
            });
            $filter->column(1, function (Filter $filter) {
                $filter->year('year');
            });
            $filter->column(1, function (Filter $filter) {
                $filter->month('month');
            });
            $filter->column(1, function (Filter $filter) {
                $filter->group('group', 'Group', function (\Poppy\MgrPage\Classes\Grid\Filter\Group $group) {
                    // 等于
                    $group->equal('=');

                    // 不等于
                    $group->notEqual('!=');

                    // 大于
                    $group->gt('>');

                    // 小于
                    $group->lt('<');

                    // 大于等于
                    $group->nlt('>=');

                    // 小于等于
                    $group->ngt('<=');

                    // 匹配
                    $group->match('*');

                    // 复杂条件
                    // $group->where('啥', function($f){
                    //     $f;
                    // });

                    // like查询
                    $group->like('%');

                    // like查询
                    $group->contains('*');

                    // ilike查询
                    $group->ilike('like');

                    // 以输入的内容开头
                    $group->startWith('start');

                    // 以输入的内容结尾
                    $group->endWith('endwith');
                });
            });
            $filter->column(2, function (Filter $filter) {
                $filter->between('created_at')->datetime();
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
                        $Action->password($item),
                        $Action->edit($item),
                    ]);
                },
            ])->fixed();
    }


    public function quickButtons(): array
    {
        return [
            $this->create(input(\Poppy\MgrPage\Classes\Grid\Filter\Scope::QUERY_NAME)),
        ];
    }


    /**
     * 创建
     * @param $type
     * @return BaseButton
     */
    public function create($type): BaseButton
    {
        $url = route_url('py-mgr-page:backend.pam.establish', null, ['type' => $type]);
        return new BaseButton('新增', $url, [
            'class' => 'J_iframe layui-btn layui-btn-sm layui-btn-normal',
        ]);
    }

    /**
     * 修改密码
     * @param $item
     * @return BaseButton
     */
    public function password($item): BaseButton
    {
        $url = route('py-mgr-page:backend.pam.password', [$item->id]);
        return new BaseButton('修改密码', $url, [
            'class' => 'J_iframe J_tooltip',
        ]);
    }


    /**
     * 编辑
     * @param $item
     * @return BaseButton
     */
    public function edit($item): BaseButton
    {
        $url = route('py-mgr-page:backend.pam.establish', [$item->id]);
        return new BaseButton("编辑[{$item->username}]", $url, [
            'class' => 'J_iframe J_tooltip',
        ]);
    }

}
