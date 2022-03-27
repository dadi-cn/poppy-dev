<?php

namespace Demo\Http\Lists;

use Closure;
use Poppy\Framework\Exceptions\ApplicationException;
use Poppy\Framework\Helper\StrHelper;
use Poppy\MgrPage\Classes\Grid\Filter;
use Poppy\MgrPage\Classes\Grid\ListBase;
use Poppy\MgrPage\Classes\Grid\Tools\BaseButton;

class ListPoppyDemo extends ListBase
{

    /**
     * @inheritDoc
     * @throws ApplicationException
     */
    public function columns()
    {
        // 自定义样式
        $this->column('id', 'ID(排序)')->sortable()->width(100);

        // 自定义样式
        $this->column('style', 'Style(Css样式)')->style('color:red;')->width(150);

        // 转换开关为 x/√
        $this->column('is_open', '图标(×/√)')->bool()->width(100);
        // 数据渲染
        // $this->column('is_open', '开关')->using([
        //     1 => '开',
        //     0 => '关',
        // ]);

        // 隐藏数据
        $this->column('updated_at', '更新时间(临时隐藏)')->width(150)->hide();

        // 自定义渲染
        $this->column('hide-email', '邮箱(隐藏显示)')->width(150)->display(function () {
            return StrHelper::hideEmail(data_get($this, 'email'));
        });
        // 邮箱
        $this->column('email', '头像[gravatar]')->gravatar()->width(130);

        // 可下载, 附加地址以及文件名称
        $this->column('file', '可下载的文件')->width(120)->downloadable();

        // 渲染状态值
        $this->column('status', '状态值')->using([
            0 => '未知',
            1 => '已下单',
            2 => '已付款',
            3 => '已取消',
        ]);

        $this->column('title', 'Str 截取(10)')->width(120)->limit(10)->ucfirst()->link();

        // 组合字段显示
        $this->column('full_name', '组合多字段(自定义渲染)')->width(180)->display(function () {
            return data_get($this, 'first_name') . ' - ' . data_get($this, 'last_name');
        });
    }


    /**
     * @inheritDoc
     * @return Closure
     */
    public function filter(): Closure
    {
        return function (Filter $filter) {
            $filter->column(1 / 12, function (Filter $filter) {
                $filter->like('username', 'username');
                $filter->where(function ($query) {
                    // this custom query
                }, 'area_id', 'username')->setPresenter(new \Poppy\Area\Classes\Grid\Filter\Presenter\Area());
            });
            $filter->column(1 / 12, function (Filter $filter) {
                $filter->equal('status')->integer();
            });
            $filter->column(1 / 12, function (Filter $filter) {
                $filter->startsWith('title');
            });
            $filter->column(1 / 12, function (Filter $filter) {
                $filter->lt('progress')->integer();
            });
            $filter->column(1 / 12, function (Filter $filter) {
                $filter->gt('progress')->integer();
            });
            $filter->column(1 / 12, function (Filter $filter) {
                $filter->day('day');
            });
            $filter->column(1 / 12, function (Filter $filter) {
                $filter->date('date');
            });
            $filter->column(1 / 12, function (Filter $filter) {
                $filter->year('year');
            });
            $filter->column(1 / 12, function (Filter $filter) {
                $filter->month('month');
            });
            $filter->column(1 / 12, function (Filter $filter) {
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
            $filter->column(1 / 12, function (Filter $filter) {
                $filter->notEqual('created_at')->datetime();
            });
        };
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
