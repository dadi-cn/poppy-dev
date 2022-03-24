<?php

namespace Poppy\MgrApp\Classes\Form\Field;

class Datetime extends Date
{
    protected string $type = 'datetime';

    protected string $format = 'YYYY-MM-DD HH:mm:ss';
}
