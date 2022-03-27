<?php

namespace Poppy\MgrPage\Classes\Grid\Filter;

use Exception;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\View\View;
use Poppy\MgrPage\Classes\Grid\Filter;
use Poppy\MgrPage\Classes\Grid\Filter\Presenter\Checkbox;
use Poppy\MgrPage\Classes\Grid\Filter\Presenter\DateTime;
use Poppy\MgrPage\Classes\Grid\Filter\Presenter\MultipleSelect;
use Poppy\MgrPage\Classes\Grid\Filter\Presenter\Presenter;
use Poppy\MgrPage\Classes\Grid\Filter\Presenter\Radio;
use Poppy\MgrPage\Classes\Grid\Filter\Presenter\Select;
use Poppy\MgrPage\Classes\Grid\Filter\Presenter\Text;
use function view;

/**
 * Class AbstractFilter.
 *
 * @method Text url()
 * @method Text email()
 * @method Text integer()
 * @method Text decimal($options = [])
 * @method Text currency($options = [])
 * @method Text percentage($options = [])
 * @method Text ip()
 * @method Text mac()
 * @method Text mobile($mask = '19999999999')
 * @method Text inputmask($options = [], $icon = '')
 * @method Text placeholder($placeholder = '')
 */
abstract class AbstractFilter extends Filter
{
    /**
     * @var Collection
     */
    public $group;
    /**
     * Element id.
     *
     * @var array|string
     */
    protected $id;
    /**
     * Label of presenter.
     *
     * @var string
     */
    protected $label;

    /**
     * @var array|string
     */
    protected $value;

    /**
     * @var array|string
     */
    protected $defaultValue;

    /**
     * @var string
     */
    protected $column;

    /**
     * Presenter object.
     *
     * @var Presenter
     */
    protected $presenter;

    /**
     * Query for filter.
     *
     * @var string
     */
    protected $query = 'where';

    /**
     * @var Filter
     */
    protected $parent;

    /**
     * @var string
     */
    protected $view = 'py-mgr-page::tpl.filter.where';

    /**
     * AbstractFilter constructor.
     *
     * @param        $column
     * @param string $label
     */
    public function __construct($column, $label = '')
    {
        $this->column = $column;
        $this->label  = $this->formatLabel($label);
        $this->id     = $this->formatId($column);

        $this->setupDefaultPresenter();
    }

    /**
     * @param Filter $filter
     */
    public function setParent(Filter $filter)
    {
        $this->parent = $filter;
    }

    /**
     * Get siblings of current filter.
     *
     * @param null $index
     *
     * @return AbstractFilter[]|mixed
     */
    public function siblings($index = null)
    {
        if (!is_null($index)) {
            return Arr::get($this->parent->filters(), $index);
        }

        return $this->parent->filters();
    }

    /**
     * Get previous filter.
     *
     * @param int $step
     *
     * @return AbstractFilter[]|mixed
     */
    public function previous($step = 1)
    {
        return $this->siblings(
            array_search($this, $this->parent->filters()) - $step
        );
    }

    /**
     * Get next filter.
     *
     * @param int $step
     *
     * @return AbstractFilter[]|mixed
     */
    public function next($step = 1)
    {
        return $this->siblings(
            array_search($this, $this->parent->filters()) + $step
        );
    }

    /**
     * Get query condition from filter.
     *
     * @param array $inputs
     *
     * @return array|mixed|null
     */
    public function condition(array $inputs)
    {
        $value = Arr::get($inputs, $this->column);

        if (!isset($value)) {
            return;
        }

        $this->value = $value;

        return $this->buildCondition($this->column, $this->value);
    }

    /**
     * Select filter.
     *
     * @param array|Collection $options
     *
     * @return Select
     */
    public function select($options = [])
    {
        return $this->setPresenter(new Select($options));
    }

    /**
     * @param array|Collection $options
     *
     * @return MultipleSelect
     */
    public function multipleSelect($options = [])
    {
        return $this->setPresenter(new MultipleSelect($options));
    }

    /**
     * @param array|Collection $options
     *
     * @return Radio
     */
    public function radio($options = [])
    {
        return $this->setPresenter(new Radio($options));
    }

    /**
     * @param array|Collection $options
     *
     * @return Checkbox
     */
    public function checkbox($options = [])
    {
        return $this->setPresenter(new Checkbox($options));
    }

    /**
     * Datetime filter.
     *
     * @param array|Collection $options
     *
     * @return DateTime
     */
    public function datetime($options = [])
    {
        return $this->setPresenter(new DateTime($options));
    }

    /**
     * Date filter.
     *
     * @return DateTime
     */
    public function date()
    {
        return $this->datetime(['layui-type' => 'date']);
    }

    /**
     * Day filter.
     *
     * @return DateTime
     */
    public function day()
    {
        return $this->datetime(['layui-type' => 'date']);
    }

    /**
     * Month filter.
     *
     * @return DateTime
     */
    public function month()
    {
        return $this->datetime(['layui-type' => 'month']);
    }

    /**
     * Year filter.
     *
     * @return DateTime
     */
    public function year()
    {
        return $this->datetime(['layui-type' => 'year']);
    }

    /**
     * Render this filter.
     *
     * @return View|string
     */
    public function render()
    {
        return view($this->view, $this->variables());
    }


    public function renderSkeleton(): array
    {
        return [
            'name'  => $this->column,
            'label' => $this->label,
            'type'  => $this->presenter->type(),
        ];
    }

    /**
     * Render this filter.
     *
     * @return View|string
     */
    public function __toString()
    {
        return $this->render();
    }

    /**
     * @param $method
     * @param $params
     *
     * @return mixed
     * @throws Exception
     *
     */
    public function __call($method, $params)
    {
        if (method_exists($this->presenter, $method)) {
            return $this->presenter()->{$method}(...$params);
        }

        throw new Exception('Method "' . $method . '" not exists.');
    }

    /**
     * Time filter.
     *
     * @return DateTime
     */
    public function time()
    {
        return $this->datetime(['layui-type' => 'time']);
    }

    /**
     * Set default value for filter.
     *
     * @param null $default
     *
     * @return $this
     */
    public function default($default = null)
    {
        if ($default) {
            $this->defaultValue = $default;
        }

        return $this;
    }

    /**
     * Get element id.
     *
     * @return array|string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set element id.
     *
     * @param string $id
     *
     * @return $this
     */
    public function setId($id)
    {
        $this->id = $this->formatId($id);

        return $this;
    }

    /**
     * Get column name of current filter.
     *
     * @return string
     */
    public function getColumn()
    {
        $parentName = $this->parent->name;

        return $parentName ? "{$parentName}_{$this->column}" : $this->column;
    }

    /**
     * Get value of current filter.
     *
     * @return array|string
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * Set presenter object of filter.
     *
     * @param Presenter $presenter
     *
     * @return mixed
     */
    public function setPresenter(Presenter $presenter)
    {
        $presenter->setParent($this);

        return $this->presenter = $presenter;
    }

    /**
     * Setup default presenter.
     *
     * @return void
     */
    protected function setupDefaultPresenter()
    {
        $this->setPresenter(new Text($this->label));
    }

    /**
     * Format label.
     *
     * @param string $label
     *
     * @return string
     */
    protected function formatLabel($label)
    {
        $label = $label ?: ucfirst($this->column);

        return str_replace(['.', '_'], ' ', $label);
    }

    /**
     * Format name.
     *
     * @param string $column
     *
     * @return string
     */
    protected function formatName($column)
    {
        $columns = explode('.', $column);

        if (count($columns) == 1) {
            $name = $columns[0];
        }
        else {
            $name = array_shift($columns);
            foreach ($columns as $column) {
                $name .= "[$column]";
            }
        }

        $parenName = $this->parent->name;

        return $parenName ? "{$parenName}_{$name}" : $name;
    }

    /**
     * Format id.
     *
     * @param $columns
     *
     * @return array|string
     */
    protected function formatId($columns)
    {
        return str_replace('.', '_', $columns);
    }

    /**
     * Get presenter object of filter.
     *
     * @return Presenter
     */
    protected function presenter()
    {
        return $this->presenter;
    }

    /**
     * Build conditions of filter.
     *
     * @return mixed
     */
    protected function buildCondition()
    {
        $column = explode('.', $this->column);

        if (count($column) == 1) {
            return [$this->query => func_get_args()];
        }

        return $this->buildRelationQuery(...func_get_args());
    }

    /**
     * Build query condition of model relation.
     *
     * @return array
     */
    protected function buildRelationQuery()
    {
        $args = func_get_args();

        [$relation, $args[0]] = explode('.', $this->column);

        return [
            'whereHas' => [
                $relation, function ($relation) use ($args) {
                    call_user_func_array([$relation, $this->query], $args);
                },
            ],
        ];
    }

    /**
     * Variables for filter view.
     *
     * @return array
     */
    protected function variables()
    {
        return array_merge([
            'id'        => $this->id,
            'column'    => $this->column,
            'name'      => $this->formatName($this->column),
            'label'     => $this->label,
            'value'     => $this->value ?: $this->defaultValue,
            'presenter' => $this->presenter(),
        ], $this->presenter()->variables());
    }
}
