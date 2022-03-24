<?php

namespace Poppy\MgrApp\Classes\Contracts;

interface Exportable
{
    /**
     * Export data from grid.
     * @return mixed
     */
    public function export();
}
