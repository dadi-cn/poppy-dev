<?php

namespace Poppy\MgrApp\Classes\Form\Field;

use Illuminate\Support\Str;
use Poppy\Framework\Validation\Rule;

class Decimal extends Text
{

    public function __construct(string $name, string $label)
    {
        parent::__construct($name, $label);
        $this->rules = [
            'regex:/^[0-9]*(\.[0-9][0-9])?$/',
            Rule::numeric(),
        ];
    }

    public function digits($decimal)
    {
        foreach ($this->rules as $k => $rule) {
            if (Str::startsWith($rule, 'regex')) {
                $this->rules[$k] = 'regex:/^[0-9]*\.[0-9]{' . $decimal . '}?$/';
            }
        }
    }
}
