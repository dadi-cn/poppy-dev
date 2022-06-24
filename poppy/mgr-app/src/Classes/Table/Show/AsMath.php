<?php

namespace Poppy\MgrApp\Classes\Table\Show;

use Poppy\Framework\Helper\UtilHelper;

trait AsMath
{


    /**
     * 显示为友好的文件大小
     * @return self
     * @deprecated 4.0-dev
     */
    public function filesize(): self
    {
        return $this->asFilesize();
    }

    /**
     * 显示为友好的文件大小
     * @return self
     */
    public function asFilesize(): self
    {
        return $this->display(function ($value) {
            return UtilHelper::formatBytes($value);
        });
    }
}
