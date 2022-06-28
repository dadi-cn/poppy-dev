<?php

namespace Demo\Http\Request\Api\Web;

use Poppy\MgrApp\Classes\Widgets\EzTableWidget;
use Poppy\System\Http\Request\ApiV1\WebApiController;

class TableController extends WebApiController
{

    /**
     * @api                    {get} api/demo/table/ez   Ez
     * @apiVersion             1.0.0
     * @apiName                TableEz
     * @apiGroup               Table
     */
    public function ez()
    {
        $headers = ['Id', 'Email', 'Name', 'Company'];
        $rows    = [
            [1, 'labore21@yahoo.com', 'Ms. Clotilde Gibson', 'Goodwin-Watsica'],
            [2, 'omnis.in@hotmail.com', 'Allie Kuhic', 'Murphy, Koepp and Morar'],
            [3, 'quia65@hotmail.com', 'Prof. Drew Heller', 'Kihn LLC'],
            [4, 'xet@yahoo.com', 'William Koss', 'Becker-Raynor'],
            [5, 'ipsa.aut@gmail.com', 'Ms. Antonietta Kozey Jr.', 'woso'],
        ];
        $form    = new EzTableWidget($headers, $rows);
        $form->setTitle('简单Table');
        return $form->resp();
    }
}
