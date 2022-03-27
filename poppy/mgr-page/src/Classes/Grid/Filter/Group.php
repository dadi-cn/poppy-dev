<?php

namespace Poppy\MgrPage\Classes\Grid\Filter;

use Closure;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use function request;

class Group extends AbstractFilter
{
    /**
     * Input value from presenter.
     *
     * @var mixed
     */
    public $input;
    /**
     * @var Closure|null
     */
    protected $builder;
    /**
     * @var string
     */
    protected $name;

    /**
     * Group constructor.
     *
     * @param string       $column
     * @param string       $label
     * @param Closure|null $builder
     */
    public function __construct($column, $label = '', Closure $builder = null)
    {
        $this->column = $column;

        if (is_callable($label) && is_null($builder)) {
            $this->builder = $label;
            $this->label   = ucfirst($this->column);
        }
        elseif (is_string($label) && is_callable($builder)) {
            $this->label   = $label;
            $this->builder = $builder;
        }

        $this->initialize();
    }

    /**
     * Filter out `equal` records.
     *
     * @param string $label
     * @param string $operator
     *
     * @return Group
     */
    public function equal($label = '', $operator = '=')
    {
        $label = $label ?: $operator;

        $condition = [$this->column, $operator, $this->value];

        return $this->joinGroup($label, $condition);
    }

    /**
     * Filter out `not equal` records.
     *
     * @param string $label
     *
     * @return Group
     */
    public function notEqual($label = '')
    {
        return $this->equal($label, '!=');
    }

    /**
     * Filter out `greater then` records.
     *
     * @param string $label
     *
     * @return Group
     */
    public function gt($label = '')
    {
        return $this->equal($label, '>');
    }

    /**
     * Filter out `less then` records.
     *
     * @param string $label
     *
     * @return Group
     */
    public function lt($label = '')
    {
        return $this->equal($label, '<');
    }

    /**
     * Specify a where query.
     *
     * @param string  $label
     * @param Closure $builder
     *
     * @return Group
     */
    public function where($label, Closure $builder)
    {
        $this->input = $this->value;

        $condition = [$builder->bindTo($this)];

        return $this->joinGroup($label, $condition);
    }

    /**
     * Specify a where like query.
     *
     * @param string $label
     * @param string $operator
     *
     * @return Group
     */
    public function like($label = '', $operator = 'like')
    {
        $label = $label ?: $operator;

        $condition = [$this->column, $operator, "%{$this->value}%"];

        return $this->joinGroup($label, $condition);
    }

    /**
     * Alias of `like` method.
     *
     * @param string $label
     *
     * @return Group
     */
    public function contains($label = '')
    {
        return $this->like($label);
    }

    /**
     * Specify a where ilike query.
     *
     * @param string $label
     *
     * @return Group
     */
    public function ilike($label = '')
    {
        return $this->like($label, 'ilike');
    }

    /**
     * @inheritDoc
     */
    public function condition(array $inputs)
    {
        $value = Arr::get($inputs, $this->column);

        if (!isset($value)) {
            return;
        }

        $this->value = $value;

        $group = Arr::get($inputs, "{$this->id}_group");

        call_user_func($this->builder, $this);

        if ($query = $this->group->get($group)) {
            return $this->buildCondition(...$query['condition']);
        }
    }

    /**
     * @inheritDoc
     */
    public function variables()
    {
        $select = request("{$this->id}_group");

        $default = $this->group->get($select) ?: $this->group->first();

        return array_merge(parent::variables(), [
            'group_name' => $this->name,
            'default'    => $default,
        ]);
    }

    /**
     * @inheritDoc
     */
    public function render()
    {
        if ($this->builder && $this->group->isEmpty()) {
            call_user_func($this->builder, $this);
        }

        return parent::render();
    }

    /**
     * Filter out `not less then` records.
     *
     * @param string $label
     *
     * @return Group
     */
    public function nlt($label = '')
    {
        return $this->equal($label, '>=');
    }

    /**
     * Filter out `not greater than` records.
     *
     * @param string $label
     *
     * @return Group
     */
    public function ngt($label = '')
    {
        return $this->equal($label, '<=');
    }

    /**
     * Filter out records that match the regex.
     *
     * @param string $label
     *
     * @return Group
     */
    public function match($label = '')
    {
        $label = $label ?: 'Match';

        return $this->equal($label, 'REGEXP');
    }

    /**
     * Filter out records which starts with input query.
     *
     * @param string $label
     *
     * @return Group
     */
    public function startWith($label = '')
    {
        $label = $label ?: 'Start with';

        $condition = [$this->column, 'like', "{$this->value}%"];

        return $this->joinGroup($label, $condition);
    }

    /**
     * Filter out records which ends with input query.
     *
     * @param string $label
     *
     * @return Group
     */
    public function endWith($label = '')
    {
        $label = $label ?: 'End with';

        $condition = [$this->column, 'like', "%{$this->value}"];

        return $this->joinGroup($label, $condition);
    }

    /**
     * Initialize a group filter.
     */
    protected function initialize()
    {
        $this->id    = $this->formatId($this->column);
        $this->group = new Collection();
        $this->name  = "{$this->id}-filter-group";

        $this->setupDefaultPresenter();
    }

    /**
     * Join a query to group.
     *
     * @param string $label
     * @param array  $condition
     *
     * @return $this
     */
    protected function joinGroup($label, array $condition)
    {
        $this->group->push(
            compact('label', 'condition')
        );

        return $this;
    }
}
