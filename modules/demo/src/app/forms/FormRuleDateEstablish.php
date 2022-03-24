<?php

namespace Demo\App\Forms;

use Poppy\Area\Action\Area;
use Poppy\Area\Models\SysArea;
use Poppy\Framework\Classes\Resp;
use Poppy\Framework\Exceptions\ApplicationException;
use Poppy\Framework\Validation\Rule;
use Poppy\MgrApp\Classes\Widgets\FormWidget;

class FormRuleDateEstablish extends FormWidget
{
    /**
     * @var SysArea
     */
    private $item;

    /**
     * 设置id
     * @param $id
     * @return $this
     */
    public function setId($id): self
    {
        $this->id = $id;

        if ($id) {
            $this->item = SysArea::find($this->id);
            if (!$this->item) {
                throw new ApplicationException('无地区信息');
            }
        }
        return $this;
    }


    public function handle()
    {
        $id   = input('id');
        $Area = new Area();
        if (is_post()) {
            if ($Area->establish(input(), $id)) {
                return Resp::success('添加版本成功', '_top_reload|1');
            }

            return Resp::error($Area->getError());
        }

        $id && $Area->initArea($id) && $Area->share();

    }

    public function data(): array
    {
        return [
            'date-code'                 => <<<CODE
\$this->text('date', 'Date')->rules([
    Rule::date(),
]);
CODE,
            'date-format-code'          => <<<CODE
\$this->text('date-format', 'DateFormat')->rules([
    Rule::dateFormat('Y-m'),
]);
CODE,
            'date-after-code'           => <<<CODE
\$this->text('date-after', 'DateAfter')->rules([
    Rule::string(),
    Rule::after('date'),
]);
CODE,
            'date-after_or_equal-code'  => <<<CODE
\$this->text('date-after_or_equal', 'DateAfterEqual')->rules([
    Rule::string(),
    Rule::afterOrEqual('date'),
]);
CODE,
            'date-before-code'          => <<<CODE
\$this->text('date-before', 'DateBefore')->rules([
    Rule::string(),
    Rule::before('date'),
]);
CODE,
            'date-before_or_equal-code' => <<<CODE
\$this->text('date-before_or_equal', 'DateBeforeEqual')->rules([
    Rule::string(),
    Rule::beforeOrEqual('date'),
]);
CODE,
        ];
    }

    public function form()
    {
        $this->text('date', 'Date')->rules([
            Rule::date(),
        ]);
        $this->code('date-code');
        $this->text('date-format', 'DateFormat')->rules([
            Rule::dateFormat('Y-m'),
        ]);
        $this->code('date-format-code');
        $this->text('date-after', 'DateAfter')->rules([
            Rule::string(),
            Rule::after('date'),
        ]);
        $this->code('date-after-code');
        $this->text('date-after_or_equal', 'DateAfterEqual')->rules([
            Rule::string(),
            Rule::afterOrEqual('date'),
        ]);
        $this->code('date-after_or_equal-code');
        $this->text('date-before', 'DateBefore')->rules([
            Rule::string(),
            Rule::before('date'),
        ]);
        $this->code('date-before-code');
        $this->text('date-before_or_equal', 'DateBeforeEqual')->rules([
            Rule::string(),
            Rule::beforeOrEqual('date'),
        ]);
        $this->code('date-before_or_equal-code');

    }
}
