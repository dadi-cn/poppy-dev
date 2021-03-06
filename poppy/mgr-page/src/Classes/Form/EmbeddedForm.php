<?php

namespace Poppy\MgrPage\Classes\Form;

use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Poppy\MgrPage\Classes\Form;

/**
 * Class EmbeddedForm.
 *
 * @method \Poppy\MgrPage\Classes\Form\Field\Text           text($column, $label = '')
 * @method \Poppy\MgrPage\Classes\Form\Field\Checkbox       checkbox($column, $label = '')
 * @method Field\Radio          radio($column, $label = '')
 * @method Field\Select         select($column, $label = '')
 * @method Field\MultipleSelect multipleSelect($column, $label = '')
 * @method Field\Textarea       textarea($column, $label = '')
 * @method \Poppy\MgrPage\Classes\Form\Field\Hidden         hidden($column, $label = '')
 * @method Field\Id             id($column, $label = '')
 * @method \Poppy\MgrPage\Classes\Form\Field\Ip             ip($column, $label = '')
 * @method Field\Url            url($column, $label = '')
 * @method \Poppy\MgrPage\Classes\Form\Field\Color          color($column, $label = '')
 * @method \Poppy\MgrPage\Classes\Form\Field\Email          email($column, $label = '')
 * @method Field\Mobile         mobile($column, $label = '')
 * @method \Poppy\MgrPage\Classes\Form\_Back\Slider         slider($column, $label = '')
 * @method \Poppy\MgrPage\Classes\Form\_Back\Map            map($latitude, $longitude, $label = '')
 * @method \Poppy\MgrPage\Classes\Form\Field\Editor         editor($column, $label = '')
 * @method Field\File           file($column, $label = '')
 * @method Field\Image          image($column, $label = '')
 * @method Field\Date           date($column, $label = '')
 * @method Field\Datetime       datetime($column, $label = '')
 * @method Field\Time           time($column, $label = '')
 * @method Field\Year           year($column, $label = '')
 * @method \Poppy\MgrPage\Classes\Form\Field\Month          month($column, $label = '')
 * @method Field\DateRange      dateRange($start, $end, $label = '')
 * @method Field\DateTimeRange  datetimeRange($start, $end, $label = '')
 * @method Field\TimeRange      timeRange($start, $end, $label = '')
 * @method Field\Number         number($column, $label = '')
 * @method \Poppy\MgrPage\Classes\Form\Field\Currency       currency($column, $label = '')
 * @method \Poppy\MgrPage\Classes\Form\_Back\HasMany        hasMany($relationName, $callback)
 * @method Field\SwitchField    switch ($column, $label = '')
 * @method \Poppy\MgrPage\Classes\Form\Field\Display        display($column, $label = '')
 * @method \Poppy\MgrPage\Classes\Form\_Back\Rate           rate($column, $label = '')
 * @method Field\Divider        divider()
 * @method \Poppy\MgrPage\Classes\Form\Field\Password       password($column, $label = '')
 * @method Field\Decimal        decimal($column, $label = '')
 * @method Field\Html           html($html, $label = '')
 * @method Field\Tags           tags($column, $label = '')
 * @method \Poppy\MgrPage\Classes\Form\_Back\Icon           icon($column, $label = '')
 * @method \Poppy\MgrPage\Classes\Form\_Back\Embeds         embeds($column, $label = '')
 * @method Field\KeyWord        keyword($column, $label = '')
 */
class EmbeddedForm
{
    /**
     * @var Form
     */
    protected $parent = null;

    /**
     * Fields in form.
     *
     * @var Collection
     */
    protected $fields;

    /**
     * Original data for this field.
     *
     * @var array
     */
    protected $original = [];

    /**
     * Column name for this form.
     *
     * @var string
     */
    protected $column;

    /**
     * EmbeddedForm constructor.
     *
     * @param string $column
     */
    public function __construct($column)
    {
        $this->column = $column;

        $this->fields = new Collection();
    }

    /**
     * Get all fields in current form.
     *
     * @return Collection
     */
    public function fields()
    {
        return $this->fields;
    }

    /**
     * Set parent form for this form.
     *
     * @param Form $parent
     *
     * @return $this
     */
    public function setParent(Form $parent)
    {
        $this->parent = $parent;

        return $this;
    }

    /**
     * Set original values for fields.
     *
     * @param array $data
     *
     * @return $this
     */
    public function setOriginal($data)
    {
        if (empty($data)) {
            return $this;
        }

        if (is_string($data)) {
            $data = json_decode($data, true);
        }

        $this->original = $data;

        return $this;
    }

    /**
     * Prepare for insert or update.
     *
     * @param array $input
     *
     * @return mixed
     */
    public function prepare($input)
    {
        foreach ($input as $key => $record) {
            $this->setFieldOriginalValue($key);
            $input[$key] = $this->prepareValue($key, $record);
        }

        return $input;
    }

    /**
     * Fill data to all fields in form.
     *
     * @param array $data
     *
     * @return $this
     */
    public function fill(array $data)
    {
        $this->fields->each(function (Field $field) use ($data) {
            $field->fill($data);
        });

        return $this;
    }

    /**
     * Add a field to form.
     *
     * @param Field $field
     *
     * @return $this
     */
    public function pushField(Field $field)
    {
        $field = $this->formatField($field);

        $this->fields->push($field);

        return $this;
    }

    /**
     * Add nested-form fields dynamically.
     *
     * @param string $method
     * @param array  $arguments
     *
     * @return Field|$this
     */
    public function __call($method, $arguments)
    {
        if ($className = Form::findFieldClass($method)) {
            $column = Arr::get($arguments, 0, '');

            /** @var Field $field */
            $field = new $className($column, array_slice($arguments, 1));

            $field->setForm($this->parent);

            $this->pushField($field);

            return $field;
        }

        return $this;
    }

    /**
     * Do prepare work for each field.
     *
     * @param string $key
     * @param string $record
     *
     * @return mixed
     */
    protected function prepareValue($key, $record)
    {
        $field = $this->fields->first(function (Field $field) use ($key) {
            return in_array($key, (array) $field->column());
        });

        if (method_exists($field, 'prepare')) {
            return $field->prepare($record);
        }

        return $record;
    }

    /**
     * Set original data for each field.
     *
     * @param string $key
     *
     * @return void
     */
    protected function setFieldOriginalValue($key)
    {
        if (array_key_exists($key, $this->original)) {
            $values = $this->original[$key];

            $this->fields->each(function (Field $field) use ($values) {
                $field->setOriginal($values);
            });
        }
    }

    /**
     * Format form, set `element name` `error key` and `element class`.
     *
     * @param Field $field
     *
     * @return Field
     */
    protected function formatField(Field $field)
    {
        $jsonKey = $field->column();

        $elementName = $elementClass = $errorKey = [];

        if (is_array($jsonKey)) {
            foreach ($jsonKey as $index => $name) {
                $elementName[$index]  = "{$this->column}[$name]";
                $errorKey[$index]     = "{$this->column}.$name";
                $elementClass[$index] = "{$this->column}_$name";
            }
        }
        else {
            $elementName  = "{$this->column}[$jsonKey]";
            $errorKey     = "{$this->column}.$jsonKey";
            $elementClass = "{$this->column}_$jsonKey";
        }

        $field->setElementName($elementName)
            ->setErrorKey($errorKey)
            ->setElementClass($elementClass);

        return $field;
    }
}
