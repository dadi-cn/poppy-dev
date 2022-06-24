<?php

namespace Poppy\MgrApp\Classes\Filter\Traits;

use Illuminate\Support\Carbon;

trait AsDatetime
{

    public function asDatetime($placeholder = ''): self
    {
        $this->type = 'datetime';
        $this->setAttribute([
            'type'   => 'datetime',
            'format' => 'YYYY-MM-DD HH:mm:ss',
        ]);
        $this->placeholder($placeholder);
        $this->width(6);
        return $this;
    }

    /**
     * 月份
     * @return $this
     */
    public function asMonth($placeholder = ''): self
    {
        $this->type = 'datetime';
        $this->setAttribute([
            'type'   => 'month',
            'format' => 'YYYY-MM',
        ]);
        $this->placeholder($placeholder);
        return $this;
    }

    /**
     * 日期
     * @return $this
     */
    public function asDate($placeholder = ''): self
    {
        $this->type = 'datetime';
        $this->setAttribute([
            'type'   => 'date',
            'format' => 'YYYY-MM-DD',
        ]);
        $this->placeholder($placeholder);
        return $this;
    }

    /**
     * 年份
     * @return $this
     */
    public function asYear($placeholder = ''): self
    {
        $this->type = 'datetime';
        $this->setAttribute([
            'type'   => 'year',
            'format' => 'YYYY',
        ]);
        $this->placeholder($placeholder);
        return $this;
    }

    /**
     * 从类型 / 值中获取开始时间作为最大时间
     * @param string $type  类型
     * @param string $value 值
     * @return void|null
     */
    protected function getStartFrom(string $value, string $type = 'datetime'): string
    {
        switch ($type) {
            case 'date':
                $end = Carbon::createFromFormat('Y-m-d', $value)->startOfDay()->toDateTimeString();
                break;
            case 'month':
                $end = Carbon::createFromFormat('Y-m', $value)->startOfMonth()->toDateTimeString();
                break;
            case 'datetime':
            default:
                $end = Carbon::parse($value)->toDateTimeString();
                break;
            case 'year':
                $end = Carbon::createFromFormat('Y', $value)->startOfYear()->toDateTimeString();
                break;
        }
        return $end;
    }

    /**
     * 获取最晚时间作为开始时间
     * @param string $value
     * @param string $type
     * @return string
     */
    protected function getEndFrom(string $value, string $type = 'datetime'): string
    {
        switch ($type) {
            case 'date':
                $start = Carbon::createFromFormat('Y-m-d', $value)->endOfDay()->toDateTimeString();
                break;
            case 'month':
                $start = Carbon::createFromFormat('Y-m', $value)->endOfMonth()->toDateTimeString();
                break;
            case 'datetime':
            default:
                $start = Carbon::parse($value)->toDateTimeString();
                break;
            case 'year':
                $start = Carbon::createFromFormat('Y', $value)->endOfYear()->toDateTimeString();
                break;
        }
        return $start;
    }
}
