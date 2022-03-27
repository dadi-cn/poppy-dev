<?php

namespace Demo\Http\Forms\Helpers;

use Poppy\Framework\Helper\TreeHelper;
use Poppy\MgrPage\Classes\Widgets\FormWidget;

class FormTreeHelper extends FormWidget
{


    /**
     * 表单标题
     * @var string
     */
    protected $title = '树状结构';


    /**
     * Build a form here.
     */
    public function form()
    {
        $Tree = new TreeHelper();
        $arr  = [
            1 => ['id' => 1, 'pid' => 0, 'name' => '一级栏目一'],
            2 => ['id' => 2, 'pid' => 0, 'name' => '一级栏目二'],
            3 => ['id' => 3, 'pid' => 1, 'name' => '二级栏目一'],
            4 => ['id' => 4, 'pid' => 1, 'name' => '二级栏目二'],
            5 => ['id' => 5, 'pid' => 2, 'name' => '三级栏目一'],
            6 => ['id' => 6, 'pid' => 2, 'name' => '三级栏目二'],
        ];
        $Tree->init($arr);
        $trees = $Tree->getTreeArray(0);
        $this->select('getParent', 'GetParent')
            ->options($trees)->help('选择一项');
    }
}
