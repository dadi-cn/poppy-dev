<?php

namespace Poppy\MgrApp\Classes\Form;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Validation\Factory;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Support\Str;
use Poppy\Framework\Helper\StrHelper;
use Poppy\MgrApp\Classes\Contracts\Structable;
use Poppy\MgrApp\Classes\Form\Traits\UseFieldAttr;
use function collect;
use function validator;

/**
 * 表单条目
 * @property-read  string $label 标签
 * @property-read  string $name  Name
 * @property-read  array $rules  规则
 */
abstract class FormItem implements Structable
{
    use UseFieldAttr;

    /**
     * 默认的数据
     * @var mixed
     */
    protected $default;

    /**
     * 规则
     * @var array
     */
    protected array $rules = [];

    /**
     * 是否将此KEY, 加入提交模型
     * @var bool
     */
    protected bool $toModel = true;

    /**
     * @var string 标签
     */
    protected string $label;

    /**
     * 字段名字
     * @var string
     */
    private string $name;

    /**
     * 对应的表单组件的类型
     * 使用的类名转换的中杠线
     * @var string
     */
    private string $type;

    /**
     * 验证器
     * @var mixed
     */
    private $validator;

    /**
     * @var string
     */
    private string $help = '';

    /**
     * 表单条目
     * @param string $name  名称/属性
     * @param string $label 标签名称
     */
    public function __construct(string $name, string $label)
    {
        $this->fieldAttrInit();
        $this->name  = $name;
        $this->label = $label;

        // element
        $this->type = StrHelper::slug(Str::afterLast(get_called_class(), '\\'));
    }

    /**
     * 设置验证规则
     * @param array $value
     * @return $this
     */
    public function rules(array $value): self
    {
        $this->rules = array_merge($value, $this->rules);
        return $this;
    }


    /**
     * 设置默认值, 当返回数据无此字段时候选择此值作为默认值
     * @param $value
     * @return FormItem
     */
    public function default($value): self
    {
        $this->default = $value;
        return $this;
    }

    public function disabled(): self
    {
        $this->setAttribute('disabled', true);
        return $this;
    }

    /**
     * 获取字段属性
     * @param $attr
     * @return string
     */
    public function __get($attr)
    {
        return $this->{$attr} ?? '';
    }

    /**
     * Get validator for this field.
     *
     * @param array $input
     *
     * @return false|Application|Factory|Validator
     */
    public function getValidator(array $input)
    {
        if ($this->validator) {
            return $this->validator->call($this, $input);
        }

        $rules = $attributes = [];

        if (!$fieldRules = $this->rules) {
            return false;
        }

        $rules[$this->name]      = $fieldRules;
        $attributes[$this->name] = $this->label;

        return validator($input, $rules, [], $attributes);
    }

    /**
     * 返回当前表单字段的结构
     * @return array
     */
    public function struct(): array
    {
        return [
            'type'  => $this->type,
            'name'  => $this->name,
            'label' => $this->label,
            'help'  => $this->help,
            'attr' => $this->attributes(),
            'rules' => collect($this->rules)->map(function ($rule) {
                return (string) $rule;
            })->toArray(),
        ];
    }


    /**
     * 帮助文本
     * @param string $help
     * @return $this
     */
    public function help(string $help = ''): self
    {
        $this->help = $help;
        return $this;
    }

    /**
     * 默认数据值
     * @return mixed
     */
    public function getDefault()
    {
        return $this->default;
    }

    /**
     * 此条件是否加入到模型
     * @return bool
     */
    public function getToModel(): bool
    {
        return $this->toModel;
    }

    /**
     * 获取属性列表
     * @return object
     */
    protected function attributes(): object
    {
        return $this->fieldAttr();
    }
}
