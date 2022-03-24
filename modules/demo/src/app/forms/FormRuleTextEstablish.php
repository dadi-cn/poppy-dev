<?php

namespace Demo\App\Forms;

use Poppy\Area\Action\Area;
use Poppy\Area\Models\SysArea;
use Poppy\Framework\Classes\Resp;
use Poppy\Framework\Exceptions\ApplicationException;
use Poppy\Framework\Validation\Rule;
use Poppy\MgrApp\Classes\Widgets\FormWidget;

class FormRuleTextEstablish extends FormWidget
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
            'str' => 'default str',


            'str-code'                   => <<<CODE
\$this->text('str', '文本:必填')->rules([
    Rule::required(),
]);
CODE,
            'str_con-code'               => <<<CODE
\$this->text('str_con', '文本:Con')->rules([
    Rule::confirmed(),
]);
CODE,
            'str_same-code'              => <<<CODE
\$this->text('str_same', '文本:Same(Con)')->rules([
    Rule::same('str_con'),
]);
CODE,
            'str_con_confirmation-code'  => <<<CODE
\$this->text('str_con_confirmation', '文本:Confirm');
CODE,
            'str_start_with-code'        => <<<CODE
\$this->text('str_start_with', '文本:StartsWith')->rules([
    Rule::startsWith('Z'),
]);
CODE,
            'str_ends_with-code'         => <<<CODE
\$this->text('str_ends_with', '文本:EndsWith')->rules([
    Rule::endsWith('Q'),
]);
CODE,
            'str-ip-code'                => <<<CODE
\$this->text('str-ip', '文本:IP')->rules([
    Rule::ip(),
]);
CODE,
            'str-ipv4-code'              => <<<CODE
\$this->text('str-ipv4', '文本:IPv4')->rules([
    Rule::ipv4(),
]);
CODE,
            'str-ipv6-code'              => <<<CODE
\$this->text('str-ipv6', '文本:IPv6')->rules([
    Rule::ipv6(),
]);
CODE,
            'str-email-code'             => <<<CODE
\$this->text('str-email', '文本:Email')->rules([
    Rule::email(),
]);
CODE,
            'str-alpha-code'             => <<<CODE
\$this->text('str-alpha', '文本:alpha')->rules([
    Rule::alpha(),
]);
CODE,
            'str-url-code'               => <<<CODE
\$this->text('str-url', '文本:Url')->rules([
    Rule::url(),
]);
CODE,
            'str-alpha-dash-code'        => <<<CODE
\$this->text('str-alpha-dash', '文本:alpha-dash')->rules([
    Rule::alphaDash(),
]);
CODE,
            'str-alpha-num-code'         => <<<CODE
\$this->text('str-alpha-num', '文本:alpha-num')->rules([
    Rule::alphaNum(),
]);
CODE,
            'str-integer-code'           => <<<CODE
\$this->text('str-integer', '文本:integer')->rules([
    Rule::integer(),
]);
CODE,
            'str-max-code'               => <<<CODE
\$this->text('str-max', '文本:(Max20)')->rules([
    Rule::string(),
    Rule::max(20),
]);
CODE,
            'str-min-code'               => <<<CODE
\$this->text('str-min', '文本:(Min5)')->rules([
    Rule::string(),
    Rule::min(5),
]);
CODE,
            'str-between-code'           => <<<CODE
\$this->text('str-between', '文本:(5-10)')->rules([
    Rule::string(),
    Rule::between(5, 10),
]);
CODE,
            'str-size-code'              => <<<CODE
\$this->text('str-size', '文本:(5)')->rules([
    Rule::string(),
    Rule::size(5),
]);
CODE,
            'str-json-code'              => <<<CODE
\$this->text('str-json', '文本:Json')->rules([
    Rule::string(),
    Rule::json(),
]);
CODE,
            'str-in-code'                => <<<CODE
\$this->text('str-in', '文本:In')->rules([
    Rule::string(),
    Rule::in(['a', 'b', 'c']),
]);
CODE,
            'str-not_in-code'            => <<<CODE
\$this->text('str-not_in', '文本:NotIn')->rules([
    Rule::string(),
    Rule::notIn(['a', 'b', 'c']),
]);
CODE,
            'number-code'                => <<<CODE
\$this->text('number', '数字')->rules([
    Rule::numeric(),
]);
CODE,
            'number-max-code'            => <<<CODE
\$this->text('number-max', '数字(Max100)')->rules([
    Rule::numeric(),
    Rule::max(100),
]);
CODE,
            'number-max-digit-code'      => <<<CODE
\$this->text('number-max-digit', '数字(Max4.28)')->rules([
    Rule::numeric(),
    Rule::max(4.28),
]);
CODE,
            'number-min-code'            => <<<CODE
\$this->text('number-min', '数字(Min20)')->rules([
    Rule::numeric(),
    Rule::min(20),
]);
CODE,
            'number-min-digit-code'      => <<<CODE
\$this->text('number-min-digit', '数字(Min2.5)')->rules([
    Rule::numeric(),
    Rule::min(2.5),
]);
CODE,
            'number-digits-code'         => <<<CODE
\$this->text('number-digits', '数字(Digits:8)')->rules([
    Rule::digits(8),
]);
CODE,
            'number-digits_between-code' => <<<CODE
\$this->text('number-digits_between', '数字(Digits:3-9)')->rules([
    Rule::digitsBetween(3, 9),
]);
CODE,
            'number-between-code'        => <<<CODE
\$this->text('number-between', '数字(20-100)')->rules([
    Rule::numeric(),
    Rule::between(20, 100),
]);
CODE,
            'number-between-digit-code'  => <<<CODE
\$this->text('number-between-digit', '数字(2.5-8)')->rules([
    Rule::numeric(),
    Rule::between(2.5, 8),
]);
CODE,
            'number-size-code'           => <<<CODE
\$this->text('number-size', '数字(5)')->rules([
    Rule::numeric(),
    Rule::size(5),
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
        $this->text('str_con', '文本:Con')->rules([
            Rule::confirmed(),
        ]);
        $this->code('str_con-code');
        $this->text('str_same', '文本:Same(Con)')->rules([
            Rule::same('str_con'),
        ]);
        $this->code('str_same-code');
        $this->text('str_con_confirmation', '文本:Confirm');
        $this->code('str_con_confirmation-code');
        $this->text('str_start_with', '文本:StartsWith')->rules([
            Rule::startsWith('Z'),
        ]);
        $this->code('str_start_with-code');
        $this->text('str_ends_with', '文本:EndsWith')->rules([
            Rule::endsWith('Q'),
        ]);
        $this->code('str_ends_with-code');
        $this->text('str-ip', '文本:IP')->rules([
            Rule::ip(),
        ]);
        $this->code('str-ip-code');
        $this->text('str-ipv4', '文本:IPv4')->rules([
            Rule::ipv4(),
        ]);
        $this->code('str-ipv4-code');
        $this->text('str-ipv6', '文本:IPv6')->rules([
            Rule::ipv6(),
        ]);
        $this->code('str-ipv6-code');
        $this->text('str-email', '文本:Email')->rules([
            Rule::email(),
        ]);
        $this->code('str-email-code');
        $this->text('str-alpha', '文本:alpha')->rules([
            Rule::alpha(),
        ]);
        $this->code('str-alpha-code');
        $this->text('str-url', '文本:Url')->rules([
            Rule::url(),
        ]);
        $this->code('str-url-code');
        $this->text('str-alpha-dash', '文本:alpha-dash')->rules([
            Rule::alphaDash(),
        ]);
        $this->code('str-alpha-dash-code');
        $this->text('str-alpha-num', '文本:alpha-num')->rules([
            Rule::alphaNum(),
        ]);
        $this->code('str-alpha-num-code');
        $this->text('str-integer', '文本:integer')->rules([
            Rule::integer(),
        ]);
        $this->code('str-integer-code');
        $this->text('str-max', '文本:(Max20)')->rules([
            Rule::string(),
            Rule::max(20),
        ]);
        $this->code('str-max-code');
        $this->text('str-min', '文本:(Min5)')->rules([
            Rule::string(),
            Rule::min(5),
        ]);
        $this->code('str-min-code');
        $this->text('str-between', '文本:(5-10)')->rules([
            Rule::string(),
            Rule::between(5, 10),
        ]);
        $this->code('str-between-code');
        $this->text('str-size', '文本:(5)')->rules([
            Rule::string(),
            Rule::size(5),
        ]);
        $this->code('str-size-code');
        $this->text('str-json', '文本:Json')->rules([
            Rule::string(),
            Rule::json(),
        ]);
        $this->code('str-json-code');
        $this->text('str-in', '文本:In')->rules([
            Rule::string(),
            Rule::in(['a', 'b', 'c']),
        ]);
        $this->code('str-in-code');
        $this->text('str-not_in', '文本:NotIn')->rules([
            Rule::string(),
            Rule::notIn(['a', 'b', 'c']),
        ]);
        $this->code('str-not_in-code');
        $this->text('number', '数字')->rules([
            Rule::numeric(),
        ]);
        $this->code('number-code');
        $this->text('number-max', '数字(Max100)')->rules([
            Rule::numeric(),
            Rule::max(100),
        ]);
        $this->code('number-max-code');
        $this->text('number-max-digit', '数字(Max4.28)')->rules([
            Rule::numeric(),
            Rule::max(4.28),
        ]);
        $this->code('number-max-digit-code');
        $this->text('number-min', '数字(Min20)')->rules([
            Rule::numeric(),
            Rule::min(20),
        ]);
        $this->code('number-min-code');
        $this->text('number-min-digit', '数字(Min2.5)')->rules([
            Rule::numeric(),
            Rule::min(2.5),
        ]);
        $this->code('number-min-digit-code');
        $this->text('number-digits', '数字(Digits:8)')->rules([
            Rule::digits(8),
        ]);
        $this->code('number-digits-code');
        $this->text('number-digits_between', '数字(Digits:3-9)')->rules([
            Rule::digitsBetween(3, 9),
        ]);
        $this->code('number-digits_between-code');
        $this->text('number-between', '数字(20-100)')->rules([
            Rule::numeric(),
            Rule::between(20, 100),
        ]);
        $this->code('number-between-code');
        $this->text('number-between-digit', '数字(2.5-8)')->rules([
            Rule::numeric(),
            Rule::between(2.5, 8),
        ]);
        $this->code('number-between-digit-code');
        $this->text('number-size', '数字(5)')->rules([
            Rule::numeric(),
            Rule::size(5),
        ]);
        $this->code('number-size-code');
    }
}
