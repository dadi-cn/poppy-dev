<?php

namespace Demo\Http\Lists;

use Closure;
use Poppy\Framework\Exceptions\ApplicationException;
use Poppy\MgrPage\Classes\Grid\Column;
use Poppy\MgrPage\Classes\Grid\Displayer\Actions;
use Poppy\MgrPage\Classes\Grid\Filter;
use Poppy\MgrPage\Classes\Grid\ListBase;
use Poppy\MgrPage\Classes\Grid\Tools\BaseButton;

class ListPoppyIndex extends ListBase
{

    /**
     * @inheritDoc
     * @throws ApplicationException
     */
    public function columns()
    {
        // 自定义样式
        $this->column('id', 'ID(排序)')->sortable()->width(100);

        // 开关
        $this->column('is_enable', 'Switch')->switch([
            1 => '打开',
            0 => '关闭',
        ]);

        // 文件链接地址
        $this->column('file', '文件链接地址')->link();

        $this->column('prefix', 'Prefix(默认前缀-Py)')->prefix('Py');

        $this->column('suffix', 'Suffix(默认后缀-Py)')->suffix('Py');
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
            $filter->column(2, function (Filter $filter) {
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
            $filter->column(1, function (Filter $filter) {
                $filter->notEqual('created_at')->datetime();
            });
            $filter->column(1, function (Filter $filter) {
                $filter->where(function ($query) {
                    $handle = input('handle');
                    $query->where('handle', 'like', "%{$handle}%");
                }, 'button', 'handle');
            });

            $filter->column(1, function (Filter $filter) {
                $filter->in('type')->multipleSelect(['user' => '用户', 'backend' => '管理员', 'develop' => '开发者']);
            });
            $filter->column(1, function (Filter $filter) {
                $filter->notIn('op_group')->multipleSelect(['play' => '伙玩', 'dailian' => '代练', 'fadan' => '发单']);
            });
            $filter->column(1, function (Filter $filter) {
                $filter->endsWith('suffix');
            });
            $filter->column(1, function (Filter $filter) {
                $filter->contains('modal');
            });
            $filter->column(0, function (Filter $filter) {
                $filter->hidden('id', '1');
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
