<?php

namespace Poppy\MgrPage\Classes\Grid;

use Closure;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Illuminate\View\View;
use InvalidArgumentException;
use Poppy\Framework\Exceptions\ApplicationException;
use Poppy\MgrPage\Classes\Grid\Filter\AbstractFilter;
use Poppy\MgrPage\Classes\Grid\Filter\Between;
use Poppy\MgrPage\Classes\Grid\Filter\BetweenDate;
use Poppy\MgrPage\Classes\Grid\Filter\Layout\Layout;
use Poppy\MgrPage\Classes\Grid\Filter\Scope;
use Poppy\MgrPage\Classes\Grid\Tools\FilterButton;
use Throwable;
use function collect;
use function request;
use function tap;
use function view;

/**
 * 筛选器
 *
 * @method AbstractFilter equal($column, $label = '')
 * @method AbstractFilter notEqual($column, $label = '')
 * @method AbstractFilter leftLike($column, $label = '')
 * @method AbstractFilter like($column, $label = '')
 * @method AbstractFilter contains($column, $label = '')
 * @method AbstractFilter startsWith($column, $label = '')
 * @method AbstractFilter endsWith($column, $label = '')
 * @method AbstractFilter ilike($column, $label = '')
 * @method AbstractFilter gt($column, $label = '')
 * @method AbstractFilter lt($column, $label = '')
 * @method Between between($column, $label = '')
 * @method BetweenDate betweenDate($column, $label = '')
 * @method AbstractFilter in($column, $label = '')
 * @method AbstractFilter notIn($column, $label = '')
 * @method AbstractFilter where($callback, $label = '', $column = null)
 * @method AbstractFilter date($column, $label = '')
 * @method AbstractFilter day($column, $label = '')
 * @method AbstractFilter month($column, $label = '')
 * @method AbstractFilter year($column, $label = '')
 * @method AbstractFilter hidden($name, $value)
 * @method AbstractFilter group($column, $label = '', $builder = null)
 */
class Filter extends FilterButton
{
    /**
     * 是否展开
     * @var bool
     */
    public $expand = false;

    /**
     * 当前的模型
     * @var Model
     */
    protected $model;

    /**
     * @var array
     */
    protected $filters = [];

    /**
     * 搜索表单的筛选条件
     *
     * @var string
     */
    protected $action;

    /**
     * @var string
     */
    protected $view = 'py-mgr-page::tpl.filter.container';

    /**
     * @var string
     */
    protected $filterId = 'filter-box';

    /**
     * @var string
     */
    protected $name = '';

    /**
     * @var Collection
     */
    protected $scopes;

    /**
     * @var Layout
     */
    protected $layout;

    /**
     * Set this filter only in the layout.
     *
     * @var bool
     */
    protected $thisFilterLayoutOnly = false;

    /**
     * Columns of filter that are layout-only.
     *
     * @var array
     */
    protected $layoutOnlyFilterColumns = [];

    /**
     * Primary key of giving model.
     *
     * @var mixed
     */
    protected $primaryKey;

    /**
     * @var array
     */
    protected static $supports = [
        'equal'       => \Poppy\MgrPage\Classes\Grid\Filter\Equal::class,
        'notEqual'    => \Poppy\MgrPage\Classes\Grid\Filter\NotEqual::class,
        'like'        => \Poppy\MgrPage\Classes\Grid\Filter\Like::class,
        'gt'          => \Poppy\MgrPage\Classes\Grid\Filter\Gt::class,
        'lt'          => \Poppy\MgrPage\Classes\Grid\Filter\Lt::class,
        'between'     => \Poppy\MgrPage\Classes\Grid\Filter\Between::class,
        'betweenDate' => \Poppy\MgrPage\Classes\Grid\Filter\BetweenDate::class,
        'group'       => \Poppy\MgrPage\Classes\Grid\Filter\Group::class,
        'where'       => \Poppy\MgrPage\Classes\Grid\Filter\Where::class,
        'in'          => \Poppy\MgrPage\Classes\Grid\Filter\In::class,
        'notIn'       => \Poppy\MgrPage\Classes\Grid\Filter\NotIn::class,
        'date'        => \Poppy\MgrPage\Classes\Grid\Filter\Date::class,
        'day'         => \Poppy\MgrPage\Classes\Grid\Filter\Day::class,
        'month'       => \Poppy\MgrPage\Classes\Grid\Filter\Month::class,
        'year'        => \Poppy\MgrPage\Classes\Grid\Filter\Year::class,
        'hidden'      => \Poppy\MgrPage\Classes\Grid\Filter\Hidden::class,
        'contains'    => \Poppy\MgrPage\Classes\Grid\Filter\Like::class,
        'startsWith'  => \Poppy\MgrPage\Classes\Grid\Filter\StartsWith::class,
        'endsWith'    => \Poppy\MgrPage\Classes\Grid\Filter\EndsWith::class,
    ];

    /**
     * Create a new filter instance.
     *
     * @param Model $model
     */
    public function __construct(Model $model)
    {
        $this->model = $model;

        $this->primaryKey = $this->model->eloquent()->getKeyName();

        $this->initLayout();

        $this->scopes = new Collection();
    }

    /**
     * Set action of search form.
     *
     * @param string $action
     *
     * @return $this
     */
    public function setAction(string $action): self
    {
        $this->action = $action;

        return $this;
    }

    /**
     * Get grid model.
     *
     * @return Model
     */
    public function getModel()
    {
        $conditions = array_merge(
            $this->conditions(),
            $this->scopeConditions()
        );

        return $this->model->addConditions($conditions);
    }

    /**
     * Get filter ID.
     *
     * @return string
     */
    public function getFilterId()
    {
        return $this->filterId;
    }

    /**
     * Set ID of search form.
     *
     * @param string $id
     *
     * @return $this
     */
    public function setFilterId($id)
    {
        $this->filterId = $id;

        return $this;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param $name
     *
     * @return $this
     */
    public function setName($name)
    {
        $this->name = $name;

        $this->setFilterId("$this->name-$this->filterId");

        return $this;
    }

    /**
     * Remove filter by filter id.
     *
     * @param mixed $id
     */
    public function removeFilterByID($id)
    {
        $this->filters = array_filter($this->filters, function (AbstractFilter $filter) use ($id) {
            return $filter->getId() != $id;
        });
    }

    /**
     * Get all conditions of the filters.
     *
     * @return array
     */
    public function conditions(): array
    {
        $inputs = Arr::dot(request()->all());

        $inputs = array_filter($inputs, function ($input) {
            return $input !== '' && !is_null($input);
        });

        $this->sanitizeInputs($inputs);

        if (empty($inputs)) {
            return [];
        }

        $params = [];

        foreach ($inputs as $key => $value) {
            Arr::set($params, $key, $value);
        }

        $conditions = [];

        foreach ($this->filters() as $filter) {
            if (in_array($column = $filter->getColumn(), $this->layoutOnlyFilterColumns)) {
                $filter->default(Arr::get($params, $column));
            }
            else {
                $conditions[] = $filter->condition($params);
            }
        }

        return array_filter($conditions);
    }

    /**
     * Set this filter layout only.
     *
     * @return $this
     */
    public function layoutOnly()
    {
        $this->thisFilterLayoutOnly = true;

        return $this;
    }

    /**
     * Use a custom filter.
     *
     * @param AbstractFilter $filter
     *
     * @return AbstractFilter
     */
    public function use(AbstractFilter $filter)
    {
        return $this->addFilter($filter);
    }

    /**
     * Get all filters.
     *
     * @return AbstractFilter[]
     */
    public function filters(): array
    {
        return $this->filters;
    }

    /**
     * @param string $key
     * @param string $label
     *
     * @return mixed
     */
    public function scope($key, $label = '')
    {
        return tap(new Scope($key, $label), function (Scope $scope) {
            return $this->scopes->push($scope);
        });
    }

    /**
     * Get all filter scopes.
     *
     * @return Collection
     */
    public function getScopes(): Collection
    {
        return $this->scopes;
    }

    /**
     * Get current scope.
     *
     * @return Scope|null
     */
    public function getCurrentScope()
    {
        $key = request(Scope::QUERY_NAME);

        return $this->scopes->first(function ($scope) use ($key) {
            return $scope->key == $key;
        });
    }

    /**
     * Add a new layout column.
     *
     * @param int|float $width
     * @param Closure   $closure
     *
     * @return $this
     */
    public function column($width, Closure $closure): self
    {
        $width = $width < 1 ? round(12 * $width) : $width;
        $this->layout->column($width, $closure);
        return $this;
    }

    /**
     * Execute the filter with conditions.
     *
     * @param bool $toArray
     *
     * @return array|Collection|mixed
     */
    public function execute($toArray = true)
    {
        if (method_exists($this->model->eloquent(), 'paginate')) {
            $this->model->usePaginate();

            return $this->model->buildData($toArray);
        }
        $conditions = array_merge(
            $this->conditions(),
            $this->scopeConditions()
        );

        return $this->model->addConditions($conditions)->buildData($toArray);
    }

    /**
     * @param callable $callback
     * @param int      $count
     *
     * @return bool
     */
    public function chunk(callable $callback, $count = 100)
    {
        $conditions = array_merge(
            $this->conditions(),
            $this->scopeConditions()
        );

        return $this->model->addConditions($conditions)->chunk($callback, $count);
    }

    /**
     * Get the string contents of the filter view.
     * @return View|string
     * @throws Throwable
     */
    public function render()
    {
        if (empty($this->filters)) {
            return '';
        }
        return view($this->view, [
            'action'    => $this->action ?: $this->urlWithoutFilters(),
            'layout'    => $this->layout,
            'filter_id' => $this->filterId,
        ])->render();
    }


    public function renderSkeleton(): array
    {
        $layout  = $this->layout;
        $columns = [];
        foreach ($layout->columns() as $column) {
            $colDef = [
                'width' => $column->width(),
            ];
            foreach ($column->filters() as $filter) {
                $colDef = array_merge($colDef, $filter->renderSkeleton());
            }

            $columns[] = $colDef;
        }
        return $columns;
    }

    /**
     * Get url without filter queryString.
     *
     * @return string
     */
    public function urlWithoutFilters()
    {
        /** @var Collection $columns */
        $columns = collect($this->filters)->map->getColumn()->flatten();

        $pageKey = 'page';

        if ($gridName = $this->model->getGrid()->getName()) {
            $pageKey = "{$gridName}_{$pageKey}";
        }

        $columns->push($pageKey);

        $groupNames = collect($this->filters)->filter(function ($filter) {
            return $filter instanceof \Poppy\MgrPage\Classes\Grid\Filter\Group;
        })->map(function (AbstractFilter $filter) {
            return "{$filter->getId()}_group";
        });

        return $this->fullUrlWithoutQuery(
            $columns->merge($groupNames)
        );
    }

    /**
     * Get url without scope queryString.
     *
     * @return string
     */
    public function urlWithoutScopes()
    {
        return $this->fullUrlWithoutQuery(Scope::QUERY_NAME);
    }

    /**
     * @param string $abstract
     * @param array  $arguments
     *
     * @return AbstractFilter
     * @throws ApplicationException
     */
    public function resolveFilter(string $abstract, array $arguments): AbstractFilter
    {
        if (!isset(static::$supports[$abstract])) {
            throw new ApplicationException('Abstract Class `' . $abstract . '` Not Exists');
        }
        return new static::$supports[$abstract](...$arguments);
    }

    /**
     * Generate a filter object and add to grid.
     *
     * @param string $method
     * @param array  $arguments
     *
     * @return AbstractFilter|$this
     * @throws ApplicationException
     */
    public function __call(string $method, array $arguments)
    {
        if ($filter = $this->resolveFilter($method, $arguments)) {
            return $this->addFilter($filter);
        }

        return $this;
    }

    /**
     * @param string $name
     * @param string $filterClass
     */
    public static function extend($name, $filterClass)
    {
        if (!is_subclass_of($filterClass, AbstractFilter::class)) {
            throw new InvalidArgumentException("The class [$filterClass] must be a type of " . AbstractFilter::class . '.');
        }

        static::$supports[$name] = $filterClass;
    }

    /**
     * Initialize filter layout.
     */
    protected function initLayout()
    {
        $this->layout = new \Poppy\MgrPage\Classes\Grid\Filter\Layout\Layout($this);
    }

    /**
     * @param $inputs
     *
     * @return array
     */
    protected function sanitizeInputs(&$inputs)
    {
        if (!$this->name) {
            return $inputs;
        }

        $inputs = collect($inputs)->filter(function ($input, $key) {
            return Str::startsWith($key, "{$this->name}_");
        })->mapWithKeys(function ($val, $key) {
            $key = str_replace("{$this->name}_", '', $key);
            return [$key => $val];
        })->toArray();
    }

    /**
     * Add a filter to grid.
     *
     * @param AbstractFilter $filter
     *
     * @return AbstractFilter
     */
    protected function addFilter(AbstractFilter $filter)
    {
        $this->layout->addFilter($filter);

        $filter->setParent($this);

        if ($this->thisFilterLayoutOnly) {
            $this->thisFilterLayoutOnly      = false;
            $this->layoutOnlyFilterColumns[] = $filter->getColumn();
        }

        return $this->filters[] = $filter;
    }

    /**
     * Get scope conditions.
     *
     * @return array
     */
    protected function scopeConditions(): array
    {
        if ($scope = $this->getCurrentScope()) {
            return $scope->condition();
        }

        return [];
    }

    /**
     * Get full url without query strings.
     *
     * @param Arrayable|array|string $keys
     *
     * @return string
     */
    protected function fullUrlWithoutQuery($keys): string
    {
        if ($keys instanceof Arrayable) {
            $keys = $keys->toArray();
        }

        $keys = (array) $keys;

        $request = request();

        $query = $request->query();
        Arr::forget($query, $keys);

        $question = $request->getBaseUrl() . $request->getPathInfo() == '/' ? '/?' : '?';

        return count($request->query()) > 0
            ? $request->url() . $question . http_build_query($query)
            : $request->fullUrl();
    }
}
