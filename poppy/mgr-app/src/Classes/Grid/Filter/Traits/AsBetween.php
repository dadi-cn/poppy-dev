<?php

namespace Poppy\MgrApp\Classes\Grid\Filter\Traits;

trait AsBetween
{

    public function asDatetimeBetween($placeholder = ''): self
    {
        $this->type = 'datetime-between';
        $this->setAttribute([
            'type'   => 'datetime',
            'format' => 'YYYY-MM-DD HH:mm:ss',
        ]);
        $this->placeholder($placeholder);
        $this->width(6);
        return $this;
    }


    /**
     * 日期
     * @return $this
     */
    public function asDateBetween($placeholder = ''): self
    {
        $this->type = 'datetime-between';
        $this->setAttribute([
            'type'   => 'date',
            'format' => 'YYYY-MM-DD',
        ]);
        $this->placeholder($placeholder);
        return $this;
    }
    /**
     * 日期
     * @return $this
     */
    public function asTextBetween($placeholder = ''): self
    {
        $this->type = 'text-between';
        $this->placeholder($placeholder);
        return $this;
    }


    public function asSelectBetween($options, $placeholder): self
    {
        $this->type = 'select-between';
        $this->options($options);
        $this->placeholder($placeholder);
        return $this;
    }
}
