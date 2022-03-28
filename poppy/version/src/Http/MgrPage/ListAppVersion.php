<?php

namespace Poppy\Version\Http\MgrPage;

use Closure;
use Poppy\Framework\Exceptions\ApplicationException;
use Poppy\MgrPage\Classes\Grid\Column;
use Poppy\MgrPage\Classes\Grid\Displayer\Actions;
use Poppy\MgrPage\Classes\Grid\Filter;
use Poppy\MgrPage\Classes\Grid\ListBase;
use Poppy\MgrPage\Classes\Grid\Tools\BaseButton;
use Poppy\Version\Models\SysAppVersion;
use function input;
use function route;
use function route_url;

class ListAppVersion extends ListBase
{

    public $title = '版本管理';

    /**
     * @inheritDoc
     * @throws ApplicationException
     */
    public function columns()
    {
        $this->column('id', "ID")->sortable()->width(80);
        $this->column('title', "版本号");
        $this->column('description', "版本描述");
        $this->column('download_url', "下载地址")->downloadable();
        $this->column('created_at', "创建时间");
    }

    /**
     * @inheritDoc
     */
    public function actions()
    {
        $this->addColumn(Column::NAME_ACTION, '操作')
            ->displayUsing(Actions::class, [
                function (Actions $actions) {
                    /** @var SysAppVersion $item */
                    $item = $actions->row;
                    $actions->append([
                        new BaseButton('<i class="fa fa-edit"></i>', route('py-version:backend.version.establish', [$item->id]), [
                            'title' => "编辑[{$item->id}]",
                            'class' => 'J_iframe',
                        ]),
                        new BaseButton('<i class="fa fa-times"></i>', route('py-version:backend.version.delete', [$item->id]), [
                            'title'        => "删除",
                            'data-confirm' => "是否要删除版本{$item->title} ?",
                            'class'        => 'text-danger J_request',
                        ]),
                    ]);
                },
            ]);
    }

    /**
     * @inheritDoc
     * @return Closure
     */
    public function filter(): Closure
    {
        return function (Filter $filter) {
            $platforms = SysAppVersion::kvType();
            foreach ($platforms as $t => $v) {
                $filter->scope($t, $v)->where('platform', $t);
            }
        };
    }

    public function quickButtons(): array
    {
        $platform = input(\Poppy\MgrPage\Classes\Grid\Filter\Scope::QUERY_NAME, SysAppVersion::PLATFORM_ANDROID);
        $desc     = SysAppVersion::kvType($platform);
        return [
            new BaseButton('<i class="fa fa-plus"></i> 新增' . $desc . '版本', route_url('py-version:backend.version.establish', null, ['platform' => $platform]), [
                'title' => "新增",
                'class' => 'J_iframe layui-btn layui-btn-sm',
            ]),
            new BaseButton('<i class="fa fa-cog"></i> 设置', route_url('py-version:backend.version.setting'), [
                'title'      => "设置",
                'data-width' => "650",
                'class'      => 'J_iframe layui-btn layui-btn-sm',
            ]),
            new BaseButton('<i class="fa fa-download"></i> 最新包地址', SysAppVersion::platformUrl($platform), [
                'title' => "最新包地址, 这里仅仅放置地址, 可能会出现地址无法访问的情况",
                'class' => 'layui-btn layui-btn-sm',
            ]),
        ];
    }
}
