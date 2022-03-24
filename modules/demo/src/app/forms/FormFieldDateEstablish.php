<?php

namespace Demo\App\Forms;

use Poppy\Framework\Classes\Resp;
use Poppy\MgrApp\Classes\Widgets\FormWidget;

class FormFieldDateEstablish extends FormWidget
{

    public function handle()
    {
        $message = print_r(input(), true);
        return Resp::success($message);
    }

    /**
     */
    public function data(): array
    {
        return [
            'id'                  => 5,
            'default'             => '',
            'year-code'          => <<<CODE
\$this->year('year', '年份');
CODE,
            'month-code'          => <<<CODE
\$this->month('month', '月份');
CODE,
            'default-code'        => <<<CODE
\$this->date('default', 'Date:默认');
CODE,
            'disabled-code'       => <<<CODE
\$this->date('disabled', 'Date:禁用')->disabled();
CODE,
            'placeholder-code'    => <<<CODE
\$this->date('placeholder', 'Date:占位符')->placeholder('Date占位符');
CODE,
            'datetime-code'       => <<<CODE
\$this->datetime('datetime', 'Datetime');
CODE,
            'time-code'           => <<<CODE
\$this->time('time', 'Time');
CODE,
            'dates-code'          => <<<CODE
\$this->dateRange('dates', '日期范围');
CODE,
            'dates-range-code'    => <<<CODE
\$this->dateRange('dates-range', '日期范围(占位符)')->placeholders('Start', 'End');
CODE,
            'monthes-code'        => <<<CODE
\$this->monthRange('monthes', '月份范围');
CODE,
            'monthes-range-code'  => <<<CODE
\$this->monthRange('monthes-range', '月份范围(占位符)')->placeholders('Start', 'End');
CODE,
            'datetime-range-code' => <<<CODE
\$this->datetimeRange('datetime-range', 'Datetime(Range)');
CODE,
            'time-range-code'     => <<<CODE
\$this->timeRange('time-range', 'Time(Range)');
CODE,
        ];
    }

    public function form()
    {
        $this->year('year', '年份');
        $this->code('year-code');
        $this->month('month', '月份');
        $this->code('month-code');
        $this->date('default', 'Date:默认');
        $this->code('default-code');
        $this->date('disabled', 'Date:禁用')->disabled();
        $this->code('disabled-code');
        $this->date('placeholder', 'Date:占位符')->placeholder('Date占位符');
        $this->code('placeholder-code');
        $this->datetime('datetime', 'Datetime');
        $this->code('datetime-code');
        $this->time('time', 'Time');
        $this->code('time-code');
        $this->dateRange('dates', '日期范围');
        $this->code('dates-code');
        $this->dateRange('dates-range', '日期范围(占位符)')->placeholders('Start', 'End');
        $this->code('dates-range-code');
        $this->monthRange('monthes', '月份范围');
        $this->code('monthes-code');
        $this->monthRange('monthes-range', '月份范围(占位符)')->placeholders('Start', 'End');
        $this->code('monthes-range-code');
        $this->datetimeRange('datetime-range', 'Datetime(Range)');
        $this->code('datetime-range-code');
        $this->timeRange('time-range', 'Time(Range)');
        $this->code('time-range-code');
    }
}
