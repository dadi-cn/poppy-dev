<?php

namespace Demo\Http\Request\Api\Web;

use Poppy\MgrApp\Classes\Widgets\SimpleTableWidget;
use Poppy\System\Http\Request\ApiV1\Web\WebApiController;

class TableController extends WebApiController
{

    /**
     * @api                    {get} api/demo/table/{:type}   [Demo]Table
     * @apiVersion             1.0.0
     * @apiName                Table
     * @apiGroup               MgrApp
     */
    public function index($auto)
    {
        $headers = ['Id', 'Email', 'Name', 'Company'];
        $rows    = [
            [1, 'labore21@yahoo.com', 'Ms. Clotilde Gibson', 'Goodwin-Watsica'],
            [2, 'omnis.in@hotmail.com', 'Allie Kuhic', 'Murphy, Koepp and Morar'],
            [3, 'quia65@hotmail.com', 'Prof. Drew Heller', 'Kihn LLC'],
            [4, 'xet@yahoo.com', 'William Koss', 'Becker-Raynor'],
            [5, 'ipsa.aut@gmail.com', 'Ms. Antonietta Kozey Jr.', 'woso'],
        ];
        $form    = new SimpleTableWidget($headers, $rows);
        $form->setTitle('简单Table');
        return $form->resp();
    }
}
