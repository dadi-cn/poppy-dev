<?php

namespace Poppy\MgrPage\Classes\Widgets;

use Closure;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\MessageBag;
use Illuminate\Support\Str;
use Illuminate\Validation\Validator;
use Poppy\Framework\Classes\Resp;
use Poppy\Framework\Classes\Traits\PoppyTrait;
use Poppy\Framework\Helper\ArrayHelper;
use Poppy\Framework\Validation\Rule;
use Poppy\MgrPage\Classes\Form as BaseForm;
use Poppy\MgrPage\Classes\Form\Field;
use Poppy\MgrPage\Classes\Layout\Content;
use function app;
use function back;
use function collect;
use function input;
use function is_post;
use function request;
use function tap;
use function view;

/**
 * Class Form.
 *
 * @method Field\Code           code($name, $label = '')
 * @method \Poppy\MgrPage\Classes\Form\Field\Text           text($name, $label = '')
 * @method \Poppy\MgrPage\Classes\Form\_Back\Button         button($label = '')
 * @method \Poppy\MgrPage\Classes\Form\Field\Link           link($label = '')
 * @method \Poppy\MgrPage\Classes\Form\Field\Password       password($name, $label = '')
 * @method \Poppy\MgrPage\Classes\Form\Field\Checkbox       checkbox($name, $label = '')
 * @method Field\Radio          radio($name, $label = '')
 * @method Field\Select         select($name, $label = '')
 * @method Field\MultipleSelect multipleSelect($name, $label = '')
 * @method Field\Textarea       textarea($name, $label = '')
 * @method \Poppy\MgrPage\Classes\Form\Field\Hidden         hidden($name, $label = '')
 * @method Field\Id             id($name, $label = '')
 * @method \Poppy\MgrPage\Classes\Form\Field\Ip             ip($name, $label = '')
 * @method Field\Url            url($name, $label = '')
 * @method \Poppy\MgrPage\Classes\Form\Field\Color          color($name, $label = '')
 * @method \Poppy\MgrPage\Classes\Form\Field\Email          email($name, $label = '')
 * @method Field\Mobile         mobile($name, $label = '')
 * @method \Poppy\MgrPage\Classes\Form\_Back\Slider         slider($name, $label = '')
 * @method Field\File           file($name, $label = '')
 * @method Field\Image          image($name, $label = '')
 * @method \Poppy\MgrPage\Classes\Form\Field\MultiImage     multiImage($name, $label = '')
 * @method Field\Date           date($name, $label = '')
 * @method Field\Datetime       datetime($name, $label = '')
 * @method Field\Time           time($name, $label = '')
 * @method Field\Year           year($column, $label = '')
 * @method \Poppy\MgrPage\Classes\Form\Field\Month          month($column, $label = '')
 * @method Field\DateRange      dateRange($start, $end, $label = '')
 * @method Field\DateTimeRange  dateTimeRange($start, $end, $label = '')
 * @method Field\TimeRange      timeRange($start, $end, $label = '')
 * @method Field\Number         number($name, $label = '')
 * @method \Poppy\MgrPage\Classes\Form\Field\Currency       currency($name, $label = '')
 * @method Field\SwitchField    switch ($name, $label = '')
 * @method \Poppy\MgrPage\Classes\Form\Field\Display        display($name, $label = '')
 * @method \Poppy\MgrPage\Classes\Form\_Back\Rate           rate($name, $label = '')
 * @method Field\Divider        divider($title = '')
 * @method \Poppy\MgrPage\Classes\Form\Field\Editor         editor($name, $label = '')
 * @method Field\Decimal        decimal($column, $label = '')
 * @method Field\Html           html($html, $arguments)
 * @method Field\Tags           tags($column, $label = '')
 * @method \Poppy\MgrPage\Classes\Form\_Back\Icon           icon($column, $label = '')
 * @method \Poppy\MgrPage\Classes\Form\_Back\Listbox        listbox($column, $label = '')
 * @method \Poppy\MgrPage\Classes\Form\_Back\Table          table($column, $label, $builder)
 * @method \Poppy\MgrPage\Classes\Form\Field\Timezone       timezone($column, $label = '')
 * @method \Poppy\MgrPage\Classes\Form\_Back\KeyValue       keyValue($column, $label = '')
 * @method Field\Keyword        keyWord($column, $label = '')
 * @method Field\Hook           hook($column, $label = '')
 * @method \Poppy\MgrPage\Classes\Form\_Back\ListField      list($column, $label = '')
 * @method mixed                handle(Request $request)
 */
class FormWidget implements Renderable
{
    use PoppyTrait;


    /**
     * @var bool
     */
    public $inbox = true;

    /**
     * @var bool 是否是 Ajax 模式提交
     */
    public $ajax = false;

    /**
     * The title of form.
     *
     * @var string
     */
    protected $title = '';

    /**
     * 是否包含 JS 加载界面
     * @var bool
     */
    protected $withContent = true;

    /**
     * @var Field[]
     */
    protected $fields = [];

    /**
     * @var array
     */
    protected $data = [];

    /**
     * @var array
     */
    protected $attributes = [];

    /**
     * Available buttons.
     *
     * @var array
     */
    protected $buttons = ['reset', 'submit'];

    /**
     *
     * @var array
     */
    protected $boxTools = [];

    /**
     * Width for label and submit field.
     *
     * @var array
     */
    protected $width = [
        'label' => 3,
        'field' => 9,
    ];
    /**
     * @var bool
     */
    private $plainSkeleton = false;

    /**
     * Form constructor.
     *
     * @param array $data
     */
    public function __construct($data = [])
    {
        $this->fill($data);

        $this->initFormAttributes();
    }

    /**
     * @return array
     */
    public function data(): array
    {
        return $this->data;
    }

    /**
     * Fill data to form fields.
     *
     * @param array $data
     *
     * @return $this
     */
    public function fill($data = [])
    {
        if ($data instanceof Arrayable) {
            $data = $data->toArray();
        }

        if (!empty($data)) {
            $this->data = $data;
        }

        return $this;
    }

    /**
     * @return $this
     */
    public function sanitize()
    {
        foreach (['_form_', '_token'] as $key) {
            request()->request->remove($key);
        }

        return $this;
    }

    /**
     * Add form attributes.
     *
     * @param string|array $attr
     * @param string       $value
     *
     * @return $this
     */
    public function attribute($attr, $value = ''): self
    {
        if (is_array($attr)) {
            foreach ($attr as $key => $value) {
                $this->attribute($key, $value);
            }
        }
        else {
            $this->attributes[$attr] = $value;
        }

        return $this;
    }

    /**
     * Format form attributes form array to html.
     *
     * @param array $attributes
     *
     * @return string
     */
    public function formatAttribute($attributes = []): string
    {
        $attributes = $attributes ?: $this->attributes;

        if ($this->hasFile()) {
            $attributes['enctype'] = 'multipart/form-data';
        }

        $html = [];
        foreach ($attributes as $key => $val) {
            $html[] = "$key=\"$val\"";
        }

        return implode(' ', $html) ?: '';
    }

    /**
     * Action uri of the form.
     *
     * @param string $action
     *
     * @return $this
     */
    public function action($action)
    {
        return $this->attribute('action', $action);
    }

    /**
     * Method of the form.
     *
     * @param string $method
     */
    public function method($method = 'POST')
    {
        if (strtolower($method) == 'put') {
            $this->hidden('_method')->default($method);

            return $this;
        }

        return $this->attribute('method', strtoupper($method));
    }

    /**
     * Disable Pjax.
     *
     * @return $this
     */
    public function disablePjax()
    {
        Arr::forget($this->attributes, 'pjax-container');

        return $this;
    }

    /**
     * Disable reset button.
     *
     * @return $this
     */
    public function disableReset()
    {
        ArrayHelper::delete($this->buttons, 'reset');

        return $this;
    }

    /**
     * Disable submit button.
     *
     * @return $this
     */
    public function disableSubmit()
    {
        ArrayHelper::delete($this->buttons, 'submit');

        return $this;
    }

    /**
     * Set field and label width in current form.
     *
     * @param int $fieldWidth
     * @param int $labelWidth
     *
     * @return $this
     */
    public function setWidth($fieldWidth = 8, $labelWidth = 2)
    {
        collect($this->fields)->each(function ($field) use ($fieldWidth, $labelWidth) {
            /* @var Field $field */
            $field->setWidth($fieldWidth, $labelWidth);
        });

        // set this width
        $this->width = [
            'label' => $labelWidth,
            'field' => $fieldWidth,
        ];

        return $this;
    }

    /**
     * Determine if the form has field type.
     *
     * @param string $name
     *
     * @return bool
     */
    public function hasField($name)
    {
        return isset(BaseForm::$availableFields[$name]);
    }

    /**
     * Add a form field to form.
     *
     * @param Field $field
     *
     * @return $this
     */
    public function pushField(Field &$field)
    {
        array_push($this->fields, $field);

        return $this;
    }

    /**
     * Get all fields of form.
     *
     * @return Field[]
     */
    public function fields()
    {
        return $this->fields;
    }

    /**
     * Determine if form fields has files.
     *
     * @return bool
     */
    public function hasFile()
    {
        foreach ($this->fields as $field) {
            if ($field instanceof Field\File) {
                return true;
            }
        }

        return false;
    }

    /**
     * Validate this form fields.
     *
     * @param Request $request
     *
     * @return bool|MessageBag
     */
    public function validate(Request $request)
    {
        $failedValidators = [];

        foreach ($this->fields() as $field) {
            if (!$validator = $field->getValidator($request->all())) {
                continue;
            }

            if (($validator instanceof Validator) && !$validator->passes()) {
                $failedValidators[] = $validator;
            }
        }

        $message = $this->mergeValidationMessages($failedValidators);
        return $message->any() ? $message : false;
    }

    /**
     * Add a fieldset to form.
     *
     * @param string  $title
     * @param Closure $setCallback
     *
     * @return \Poppy\MgrPage\Classes\Form\_Back\Fieldset
     */
    public function fieldset(string $title, Closure $setCallback)
    {
        $fieldset = new \Poppy\MgrPage\Classes\Form\_Back\Fieldset();

        $this->html($fieldset->start($title))->plain();

        $setCallback($this);

        $this->html($fieldset->end())->plain();

        return $fieldset;
    }

    public function unbox()
    {
        $this->inbox = false;

        return $this;
    }


    public function boxTools($tools)
    {
        $this->boxTools = array_merge($this->boxTools, $tools);
        return $this;
    }

    public function plainSkeleton()
    {
        $this->plainSkeleton = true;
    }

    /**
     * Render the form.
     */
    public function render()
    {
        if (method_exists($this, 'form')) {
            $this->form();
        }

        if (method_exists($this, 'handle')) {
            $this->method();
            $this->action(app('url')->current());
        }

        if (input('_skeleton')) {
            if ($this->plainSkeleton) {
                return $this->fetchSkeleton();
            }
            return Resp::success('Success', $this->fetchSkeleton());
        }

        if (is_post()) {
            $request = $this->pyRequest();
            if ($errors = $this->validate($request)) {
                if ($this->ajax) {
                    return Resp::error($errors);
                }
                else {
                    return back()->withInput()->withErrors($errors);
                }
            }
            return $this->sanitize()->handle($request);
        }

        $form = view('py-mgr-page::tpl.widgets.form', $this->getVariables())->render();

        if (!($title = $this->title()) || !$this->inbox) {
            if ($this->withContent) {
                return (new Content())->body($form);
            }
            return $form;
        }

        // init box and render
        $box = (new BoxWidget($title, $form));

        $box->tools($this->boxTools);
        if ($this->withContent) {
            return (new Content())->body($box->render());
        }
        return $box->render();
    }

    /**
     * Generate a Field object and add to form builder if Field exists.
     *
     * @param string $method
     * @param array  $arguments
     *
     * @return Field|$this
     */
    public function __call($method, $arguments)
    {
        if (!$this->hasField($method)) {
            return $this;
        }

        $class = BaseForm::$availableFields[$method];

        $name   = Arr::get($arguments, 0);
        $params = array_slice($arguments, 1);
        $field  = new $class($name, $params);
        return tap($field, function ($field) {
            $this->pushField($field);
        });
    }

    /**
     * Get form title.
     *
     * @return string
     */
    public function title(): string
    {
        return $this->title;
    }

    public function fetchSkeleton(): array
    {
        collect($this->fields())->each->fill($this->data());

        $fields = [];
        foreach ($this->fields() as $field) {
            $variable = $field->variables();
            $opts     = [
                'name'        => $variable['name'],
                'type'        => $field->getType(),
                'value'       => $variable['value'],
                'label'       => $variable['label'],
                'placeholder' => $variable['placeholder'],
                'rules'       => $variable['rules'],
                'help'        => $variable['help']['text'] ?? '',
            ];

            // options
            $options = (array) $variable['options'];
            if (count($options)) {
                $newOption = [];
                foreach ($options as $key => $option) {
                    $newOption[] = [
                        'key'   => $key,
                        'value' => $option,
                    ];
                }
                $options = $newOption;
                $opts    = array_merge($opts, [
                    'options' => $options,
                ]);
            }
            $fields[] = array_merge($opts, $field->skeleton());

        }
        return [
            'type'    => 'form',
            'title'   => $this->title,
            'fields'  => $fields,
            'action'  => $this->attributes['action'],
            'method'  => $this->attributes['method'],
            'buttons' => $this->buttons,
        ];
    }

    /**
     * Initialize the form attributes.
     */
    protected function initFormAttributes()
    {
        $this->attributes = [
            'method'         => 'POST',
            'action'         => '',
            'class'          => 'layui-form-auto layui-form layui-form-sm',
            'accept-charset' => 'UTF-8',
            'pjax-container' => true,
            'id'             => 'j_form_' . Str::random(4),
        ];
    }

    /**
     * Get variables for render form.
     *
     * @return array
     */
    protected function getVariables(): array
    {
        collect($this->fields())->each->fill($this->data());

        return [
            'fields'     => $this->fields,
            'attributes' => $this->formatAttribute(),
            'validation' => $this->getJqValidation(),
            'action'     => $this->attributes['action'],
            'method'     => $this->attributes['method'],
            'buttons'    => $this->buttons,
            'width'      => $this->width,
            'ajax'       => $this->ajax,
            'id'         => $this->attributes['id'],
        ];
    }

    /**
     * Merge validation messages from input validators.
     *
     * @param Validator[] $validators
     *
     * @return MessageBag
     */
    protected function mergeValidationMessages($validators)
    {
        $messageBag = new MessageBag();

        foreach ($validators as $validator) {
            $messageBag = $messageBag->merge($validator->messages());
        }

        return $messageBag;
    }

    /**
     * 获取 Jquery Validation
     * @return false|string
     */
    private function getJqValidation()
    {
        $rules    = [];
        $messages = [];

        $funJqRules = function (array $rules, Field $field) {
            $jqRules = [];
            foreach ($rules as $rule) {
                if ($rule === Rule::required()) {
                    $jqRules['required'] = true;
                }
                if ($rule === Rule::numeric()) {
                    $jqRules['number'] = true;
                }
                if ($rule === Rule::email()) {
                    $jqRules['email'] = true;
                }
                if ($rule === Rule::mobile()) {
                    $jqRules['mobile'] = true;
                }
                if ($rule === Rule::ip()) {
                    $jqRules['ipv4'] = true;
                }
                if ($rule === Rule::url()) {
                    $jqRules['url'] = true;
                }
                if ($rule === Rule::alpha()) {
                    $jqRules['alpha'] = true;
                }
                if ($rule === Rule::alphaDash()) {
                    $jqRules['alpha_dash'] = true;
                }
                // 相等判定
                if (Str::contains($field->column(), '_confirmation')) {
                    $jqRules['equalTo'] = '#' . Str::replaceLast('_confirmation', '', $field->formatName($field->column()));
                }
                if (Str::contains($rule, 'regex')) {
                    $rule             = Str::replaceFirst('/', '', Str::after($rule, 'regex:'));
                    $jqRules['regex'] = Str::replaceLast('/', '', $rule);
                }

                if (in_array(Rule::numeric(), $rules)) {
                    if (in_array('min', $rules)) {
                        $jqRules['min'] = (int) Str::after($rule, 'min:');
                    }
                }

                if (Str::contains($rule, 'min')) {
                    if (in_array(Rule::numeric(), $rules)) {
                        $jqRules['min'] = (int) Str::after($rule, 'min:');
                    }
                    else {
                        $jqRules['minlength'] = (int) Str::after($rule, 'min:');
                    }
                }
                if (Str::contains($rule, 'max')) {
                    if (in_array(Rule::numeric(), $rules)) {
                        $jqRules['max'] = (int) Str::after($rule, 'max:');
                    }
                    else {
                        $jqRules['maxlength'] = (int) Str::after($rule, 'max:');
                    }
                }
            }


            return $jqRules;
        };
        collect($this->fields())->each(function (Field $field) use (&$rules, &$messages, $funJqRules) {
            if (count($field->getRules())) {
                $jqRules = $funJqRules($field->getRules(), $field);
                if (count($jqRules)) {
                    $name = $field->formatName($field->column());
                    if ($field instanceof \Poppy\MgrPage\Classes\Form\Field\Checkbox) {
                        $name .= '[]';
                    }
                    $rules[$name] = $jqRules;
                }
            }

            if (count($field->getValidationMessages())) {
                $messages[$field->column()] = $field->getValidationMessages();
            }
        });

        $jqValidation = [
            'rules'    => $rules,
            'messages' => $messages,
        ];
        return json_encode($jqValidation, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
    }
}
