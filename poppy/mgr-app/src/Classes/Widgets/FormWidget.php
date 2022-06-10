<?php

namespace Poppy\MgrApp\Classes\Widgets;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Fluent;
use Illuminate\Support\MessageBag;
use Illuminate\Validation\Validator;
use Poppy\Framework\Classes\Resp;
use Poppy\Framework\Classes\Traits\PoppyTrait;
use Poppy\Framework\Exceptions\ApplicationException;
use Poppy\Framework\Helper\ArrayHelper;
use Poppy\MgrApp\Classes\Form\Field\Actions;
use Poppy\MgrApp\Classes\Form\Field\Checkbox;
use Poppy\MgrApp\Classes\Form\Field\Code;
use Poppy\MgrApp\Classes\Form\Field\Color;
use Poppy\MgrApp\Classes\Form\Field\Currency;
use Poppy\MgrApp\Classes\Form\Field\Date;
use Poppy\MgrApp\Classes\Form\Field\DateRange;
use Poppy\MgrApp\Classes\Form\Field\Datetime;
use Poppy\MgrApp\Classes\Form\Field\DatetimeRange;
use Poppy\MgrApp\Classes\Form\Field\Decimal;
use Poppy\MgrApp\Classes\Form\Field\Divider;
use Poppy\MgrApp\Classes\Form\Field\Dynamic;
use Poppy\MgrApp\Classes\Form\Field\Editor;
use Poppy\MgrApp\Classes\Form\Field\Email;
use Poppy\MgrApp\Classes\Form\Field\File;
use Poppy\MgrApp\Classes\Form\Field\Image;
use Poppy\MgrApp\Classes\Form\Field\Ip;
use Poppy\MgrApp\Classes\Form\Field\Mobile;
use Poppy\MgrApp\Classes\Form\Field\Month;
use Poppy\MgrApp\Classes\Form\Field\MonthRange;
use Poppy\MgrApp\Classes\Form\Field\MultiFile;
use Poppy\MgrApp\Classes\Form\Field\MultiImage;
use Poppy\MgrApp\Classes\Form\Field\MultiSelect;
use Poppy\MgrApp\Classes\Form\Field\Number;
use Poppy\MgrApp\Classes\Form\Field\OnOff;
use Poppy\MgrApp\Classes\Form\Field\Password;
use Poppy\MgrApp\Classes\Form\Field\Radio;
use Poppy\MgrApp\Classes\Form\Field\Select;
use Poppy\MgrApp\Classes\Form\Field\Tags;
use Poppy\MgrApp\Classes\Form\Field\Text;
use Poppy\MgrApp\Classes\Form\Field\Textarea;
use Poppy\MgrApp\Classes\Form\Field\Time;
use Poppy\MgrApp\Classes\Form\Field\TimeRange;
use Poppy\MgrApp\Classes\Form\Field\Url;
use Poppy\MgrApp\Classes\Form\Field\Year;
use Poppy\MgrApp\Classes\Form\FieldDef;
use Poppy\MgrApp\Classes\Form\FormItem;
use Poppy\MgrApp\Classes\Traits\UseWidgetUtil;
use function app;
use function tap;

/**
 * Form Widget
 * @url https://element-plus.gitee.io/zh-CN/component/form.html#form-attributes
 * @method Text text($name, $label = '')
 * @method Textarea textarea($name, $label = '')
 * @method Url url($name, $label = '')
 * @method Password password($name, $label = '')
 * @method Mobile mobile($name, $label = '')
 * @method Ip ip($name, $label = '')
 * @method Decimal decimal($name, $label = '')
 * @method Currency currency($name, $label = '')
 * @method Email email($name, $label = '')
 * @method Number number($name, $label = '')
 * @method Radio radio($name, $label = '')
 * @method Checkbox checkbox($name, $label = '')
 * @method Select select($name, $label = '')
 * @method MultiSelect multiSelect($name, $label = '')
 * @method Tags tags($name, $label = '')
 * @method Color color($name, $label = '')
 * @method Year year($name, $label = '')
 * @method Month month($name, $label = '')
 * @method Date date($name, $label = '')
 * @method Datetime datetime($name, $label = '')
 * @method Time time($name, $label = '')
 * @method DateRange dateRange($name, $label = '')
 * @method MonthRange monthRange($name, $label = '')
 * @method DatetimeRange datetimeRange($name, $label = '')
 * @method TimeRange timeRange($name, $label = '')
 * @method OnOff onOff($name, $label = '')
 * @method Image image($name, $label = '')
 * @method File file($name, $label = '')
 * @method MultiImage multiImage($name, $label = '')
 * @method MultiFile multiFile($name, $label = '')
 * @method Editor editor($name, $label = '')
 * @method Dynamic dynamic($name, $label = '')
 * @method Divider divider($label)
 * @method Code code($name, $label = '')
 * @method Actions actions($name, $label = '')
 */
abstract class FormWidget
{
    use PoppyTrait;
    use UseWidgetUtil;

    /**
     * 表单标题
     * @var string
     */
    protected string $title = '';

    /**
     * 表单内的表单条目集合
     * @var Collection
     */
    protected Collection $items;

    /**
     * 模型数据
     * @var array
     */
    protected array $model = [];

    /**
     * 表单属性
     * @var Fluent
     */
    protected Fluent $attrs;

    /**
     * 可用的按钮
     * @var array
     */
    protected array $buttons = ['reset', 'submit'];


    /**
     * Form constructor.
     */
    public function __construct()
    {
        $this->attrs = new Fluent();
        $this->items = new Collection();

        // 默认是非行内模式, 默认宽度
        $this->attrs->offsetSet('label-width', 'auto');
    }

    public abstract function form();

    public abstract function handle();

    public function data(): array
    {
        return [];
    }

    /**
     * 表单域标签的宽度，例如 '50px'。 作为 Form 直接子元素的 form-item 会继承该值。 支持 auto
     * @param string $width
     */
    public function labelWidth(string $width)
    {
        $this->attrs->offsetSet('label-width', $width);
    }

    /**
     * Disable reset button.
     * @return $this
     */
    public function disableReset(): self
    {
        ArrayHelper::delete($this->buttons, 'reset');
        return $this;
    }

    /**
     * Disable submit button.
     *
     * @return $this
     */
    public function disableSubmit(): self
    {
        ArrayHelper::delete($this->buttons, 'submit');
        return $this;
    }


    /**
     * 行内表单模式, 取消自动宽度, 进行宽度自适应
     * @return $this
     */
    public function inline(): self
    {
        $this->attrs->offsetSet('inline', true);
        $this->attrs->offsetSet('label-width', '');
        return $this;
    }


    /**
     * 是否禁用该表单内的所有组件。 若设置为 true，则表单内组件上的 disabled 属性不再生效
     * @param bool $value
     * @return $this
     */
    public function disabled(bool $value): self
    {
        $this->attrs->offsetSet('disabled', $value);
        return $this;
    }

    /**
     * 向服务器添加字段
     * @param FormItem $item
     * @return $this
     */
    public function addItem(FormItem $item): self
    {
        $this->items->push($item);
        return $this;
    }

    /**
     * 获取表单的所有字段
     * @return FormItem[]|Collection
     */
    public function items($type = ''): Collection
    {
        if ($type === 'model') {
            return $this->items->filter(function (FormItem $item) {
                return $item->getToModel();
            });
        }
        return $this->items;
    }

    /**
     * Generate items and append to form list
     * @param string $method 类型
     * @param array $arguments 传入的参数
     *
     * @return FormItem|$this
     * @throws ApplicationException
     */
    public function __call(string $method, array $arguments = [])
    {
        $name  = (string) Arr::get($arguments, 0);
        $label = (string) Arr::get($arguments, 1);
        $field = FieldDef::create($method, $name, $label);
        if (is_null($field)) {
            throw new ApplicationException("Field `${method}` not exists");
        }
        return tap($field, function ($field) {
            $this->addItem($field);
        });
    }

    /**
     * 设置表单的标题
     * @param string $value
     * @return FormWidget
     */
    public function title(string $value): self
    {
        $this->title = $value;
        return $this;
    }

    /**
     * 返回表单的结构
     * 规则解析参考 : https://github.com/yiminghe/async-validator
     * @return JsonResponse|RedirectResponse|Response
     */
    public function resp()
    {
        $request = app('request');
        // 组建 Form 表单
        $this->form();

        if ($this->queryHas('submit')) {
            $message = $this->validate($request->all());
            if ($message instanceof MessageBag) {
                return Resp::error($message);
            }
            return $this->handle();
        }

        $struct = [];
        if ($this->queryHas('depend')) {
            $use    = $this->queryAfter('depend');
            $name   = input('name', '');
            $params = (string) input('params');
            $values = (array) input('values');
            if ($use === 'attr') {
                $struct = FieldDef::fetchDependAttr($name, $params);
            }
            else {
                $struct = FieldDef::fetchDependField($name, $params, $values);
            }
        }
        if ($this->queryHas('struct')) {
            $struct = array_merge($struct, $this->queryStruct());
        }
        if ($this->queryHas('data')) {
            $struct = array_merge($struct, [
                'model' => $this->queryData()
            ]);
        }
        return Resp::success(input('_query') ?: '', $struct);
    }

    /**
     * 获取路由参数
     * @param $param
     * @return object|string
     */
    public function param($param)
    {
        return app('router')->current()->parameter($param);
    }

    /**
     * 查询结构
     * @return array
     */
    public function queryStruct(): array
    {
        $items = new Collection();
        $this->items->each(function (FormItem $item) use ($items) {
            $struct = $item->struct();
            $items->push($struct);
        });
        return [
            'type'    => 'form',
            'title'   => $this->title,
            'buttons' => $this->buttons,
            'attr'    => (object) $this->attrs->toArray(),
            'items'   => $items->toArray(),
        ];
    }

    /**
     * 查询数据
     */
    public function queryData(): object
    {
        $this->fill($this->data());
        return (object) $this->model;
    }

    /**
     * Validate this form fields.
     * @param array $input 输入待验证的值
     * @return bool|MessageBag
     */
    public function validate(array $input = [])
    {
        $failed = [];

        foreach ($this->items() as $field) {
            if (!$validator = $field->getValidator($input)) {
                continue;
            }

            if (($validator instanceof Validator) && !$validator->passes()) {
                $failed[] = $validator;
            }
        }

        $messageBag = new MessageBag();
        foreach ($failed as $valid) {
            $messageBag = $messageBag->merge($valid->messages());
        }
        return $messageBag->any() ? $messageBag : false;
    }

    /**
     * Fill data to form fields.
     *
     * @param array $data
     * @return $this
     */
    private function fill(array $data = []): self
    {
        if (!empty($data)) {
            $this->model = $data;
        }

        return $this;
    }
}
