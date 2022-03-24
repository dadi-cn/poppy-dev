<?php

namespace Demo\App\Forms;

use Poppy\Area\Action\Area;
use Poppy\Area\Models\SysArea;
use Poppy\Framework\Classes\Resp;
use Poppy\Framework\Exceptions\ApplicationException;
use Poppy\Framework\Validation\Rule;
use Poppy\MgrApp\Classes\Widgets\FormWidget;

class FormRuleRequiredEstablish extends FormWidget
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
            'id'  => 5,
            'str' => 'default title',

            'str-code' => <<<CODE
\$this->text('str', '文本:必填')->rules([
    Rule::required(),
]);
CODE,
            'required-code' => <<<CODE
\$this->text('required', 'Required:(Bool)')->rules([
    Rule::requiredIf(true),
]);
CODE,
            'required-if-code' => <<<CODE
\$this->text('required-if', 'RequiredIf:(Str:a,b,c)')->rules([
    Rule::requiredIf('str', ['a', 'b', 'c']),
]);
CODE,
            'required-unless-code' => <<<CODE
\$this->text('required-unless', 'RequiredUnless:(Str:a,b,c)')->rules([
    Rule::requiredUnless('str', ['a', 'b', 'c']),
]);
CODE,
            'a-code' => <<<CODE
\$this->text('a', 'RequiredBase:A')->rules([

]);
CODE,
            'b-code' => <<<CODE
\$this->text('b', 'RequiredBase:B')->rules([

]);
CODE,
            'required-with-code' => <<<CODE
\$this->text('required-with', 'With:(a,b)')->rules([
    Rule::requiredWith('a,b'),
]);
CODE,
            'required-with-all-code' => <<<CODE
\$this->text('required-with-all', 'WithAll:(a,b)')->rules([
    Rule::requiredWithAll(['a', 'b']),
]);
CODE,
            'required-without-code' => <<<CODE
\$this->text('required-without', 'Without:(a,b)')->rules([
    Rule::requiredWithout(['a', 'b']),
]);
CODE,
            'required-without-all-code' => <<<CODE
\$this->text('required-without-all', 'WithoutAll:(a,b)')->rules([
    Rule::requiredWithoutAll(['a', 'b']),
]);
CODE,
        ];
    }

    public function form()
    {
        $this->text('str', '文本:必填')->rules([
        Rule::required(),
]);
            $this->code('str-code');
$this->text('required', 'Required:(Bool)')->rules([
    Rule::requiredIf(true),
]);
    $this->code('required-code');
$this->text('required-if', 'RequiredIf:(Str:a,b,c)')->rules([
    Rule::requiredIf('str', ['a', 'b', 'c']),
]);
    $this->code('required-if-code');
$this->text('required-unless', 'RequiredUnless:(Str:a,b,c)')->rules([
    Rule::requiredUnless('str', ['a', 'b', 'c']),
]);
    $this->code('required-unless-code');
$this->text('a', 'RequiredBase:A')->rules([

]);
    $this->code('a-code');
$this->text('b', 'RequiredBase:B')->rules([

]);
    $this->code('b-code');
$this->text('required-with', 'With:(a,b)')->rules([
    Rule::requiredWith('a,b'),
]);
    $this->code('required-with-code');
$this->text('required-with-all', 'WithAll:(a,b)')->rules([
    Rule::requiredWithAll(['a', 'b']),
]);
    $this->code('required-with-all-code');
$this->text('required-without', 'Without:(a,b)')->rules([
    Rule::requiredWithout(['a', 'b']),
]);
    $this->code('required-without-code');
$this->text('required-without-all', 'WithoutAll:(a,b)')->rules([
    Rule::requiredWithoutAll(['a', 'b']),
]);
    $this->code('required-without-all-code');
    }
}
