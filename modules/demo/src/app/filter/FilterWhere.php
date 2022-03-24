<?php


namespace Demo\App\Filter;

use Poppy\Framework\Exceptions\ApplicationException;
use Poppy\MgrApp\Classes\Grid\Filter\Query\Where;
use Poppy\MgrApp\Classes\Widgets\FilterWidget;
use Poppy\MgrApp\Classes\Widgets\TableWidget;
use Poppy\MgrApp\Http\Grid\GridBase;

/**
 * 按钮
 */
class FilterWhere extends GridBase
{
    public string $title = 'Where';

    /**
     * @inheritDoc
     */
    public function table(TableWidget $table)
    {
        $table->add('id', 'ID')->quickId();
        $table->add('title', '标题')->width(200)->ellipsis();
        $table->add('status', '状态')->display(function () {
            $defs = [
                1 => '未发布',
                2 => '草稿',
                5 => '待审核',
                3 => '已发布',
                4 => '已删除',
            ];
            return $defs[data_get($this, 'status')] ?? '-';
        });
        $table->add('is_open', '状态')->display(function () {
            $defs = [
                0 => '禁用',
                1 => '启用',
            ];
            return $defs[data_get($this, 'is_open')] ?? '-';
        });
        $table->add('birth_at', '发布时间');
    }

    /**
     * @inheritDoc
     */
    public function filter(FilterWidget $filter)
    {
        $filter->where(function ($query) {
            /** @var Where $this */
            $query->where('title', 'like', "%{$this->value}%");
        }, '标题')->asText('标题模糊搜索');
        $filter->where(function ($query) {
            /** @var Where $this */
            if (!$this->value) {
                return $query;
            }
            $query->whereIn('status', $this->value);
        }, '标题')->asMultiSelect([
            1 => '未发布',
            2 => '草稿',
            5 => '待审核',
            3 => '已发布',
            4 => '已删除',
        ], '状态');
        $filter->where(function ($query) {
            /** @var Where $this */
            if (is_numeric($this->value)) {
                $query->where('is_open', $this->value);
            }
        }, '开关')->asSelect([
            0 => '关闭',
            1 => '开启',
        ], '开关');
        $filter->where(function ($query) {
        }, '状态范围')->asSelectBetween([
            1 => '未发布',
            2 => '草稿',
            5 => '待审核',
            3 => '已发布',
            4 => '已删除',
        ], '开关');
        $filter->where(function ($query) {
        }, 'Date')->asDate('Date');
        $filter->where(function ($query) {
        }, 'Datetime')->asDatetime('Datetime');
        $filter->where(function ($query) {
        }, 'Month')->asMonth('Month');
        $filter->where(function ($query) {
        }, 'Year')->asYear('Year');
        $filter->where(function ($query) {
        }, 'TextBetween')->asTextBetween(['S', 'E']);
        $filter->where(function ($query) {
        }, 'DateBetween')->asDateBetween(['S', 'E']);
        $filter->where(function ($query) {
        }, 'DateTimeBetween')->asDatetimeBetween(['S', 'E']);
        $filter->action();
    }
}
