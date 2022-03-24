<?php

namespace Poppy\MgrApp\Classes\Contracts;

interface Respable
{
    /**
     * 支持 code 以及 message 的返回
     */
    public function resp();
}
