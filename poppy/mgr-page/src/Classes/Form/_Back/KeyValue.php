<?php

namespace Poppy\MgrPage\Classes\Form\_Back;

use Illuminate\Support\Arr;
use Poppy\MgrPage\Classes\Form\Field;
use function __;
use function validator;

class KeyValue extends Field
{
    /**
     * @var array
     */
    protected $value = ['' => ''];

    /**
     * Fill data to the field.
     *
     * @param array $data
     *
     * @return void
     */
    public function fill($data)
    {
        $this->data = $data;

        $this->value = Arr::get($data, $this->column, $this->value);

        $this->formatValue();
    }

    /**
     * @inheritDoc
     */
    public function getValidator(array $input)
    {
        if ($this->validator) {
            return $this->validator->call($this, $input);
        }

        if (!is_string($this->column)) {
            return false;
        }

        $rules = $attributes = [];

        if (!$fieldRules = $this->getRules()) {
            return false;
        }

        if (!Arr::has($input, $this->column)) {
            return false;
        }

        $rules["{$this->column}.keys.*"]        = 'distinct';
        $rules["{$this->column}.values.*"]      = $fieldRules;
        $attributes["{$this->column}.keys.*"]   = __('Key');
        $attributes["{$this->column}.values.*"] = __('Value');

        return validator($input, $rules, $this->getValidationMessages(), $attributes);
    }

    public function prepare($value)
    {
        return array_combine($value['keys'], $value['values']);
    }
}
