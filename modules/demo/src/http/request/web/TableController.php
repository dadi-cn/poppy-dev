<?php

namespace Demo\Http\Request\Web;

use Demo\Http\Lists\ListPoppyDemo;
use Demo\Http\Lists\ListPoppyEditable;
use Demo\Http\Lists\ListPoppyIndex;
use Demo\Http\Lists\ListPoppyUser;
use Demo\Models\DemoWebapp;
use Poppy\Framework\Exceptions\ApplicationException;
use Poppy\MgrPage\Classes\Grid;
use Poppy\MgrPage\Classes\Widgets\TableWidget;
use Poppy\System\Http\Request\Web\WebController;
use Throwable;

/**
 * 内容生成器
 */
class TableController extends WebController
{

    /**
     * 主页
     * @throws Throwable
     */
    public function index()
    {
        // table 1
        $headers = ['Id', 'Email', 'Name', 'Company'];
        $rows    = [
            [1, 'labore21@yahoo.com', 'Ms. Clotilde Gibson', 'Goodwin-Watsica'],
            [2, 'omnis.in@hotmail.com', 'Allie Kuhic', 'Murphy, Koepp and Morar'],
            [3, 'quia65@hotmail.com', 'Prof. Drew Heller', 'Kihn LLC'],
            [4, 'xet@yahoo.com', 'William Koss', 'Becker-Raynor'],
            [5, 'ipsa.aut@gmail.com', 'Ms. Antonietta Kozey Jr.', 'woso'],
        ];

        $table = new TableWidget($headers, $rows);

        return $table->render();
    }

    /**
     * @throws Throwable
     * @throws ApplicationException
     */
    public function demo($type)
    {
        // 第一列显示id字段，并将这一列设置为可排序列
        $grid = new Grid(new DemoWebapp());
        $grid->setTitle('Title');
        if ($type === 'demo') {
            $grid->setLists(ListPoppyDemo::class);
        }
        if ($type === 'edit') {
            $grid->setLists(ListPoppyEditable::class);
        }
        if ($type === 'index') {
            $grid->setLists(ListPoppyIndex::class);
        }
        if ($type === 'user') {
            $grid->setLists(ListPoppyUser::class);
        }
        return $grid->render();
    }
}