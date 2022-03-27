<?php

namespace Poppy\MgrPage\Classes\Grid;

use Carbon\Carbon;
use Closure;
use Exception;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Database\Eloquent\Model as BaseModel;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use InvalidArgumentException;
use Poppy\Framework\Helper\UtilHelper;
use Poppy\MgrPage\Classes\Actions\RowAction;
use Poppy\MgrPage\Classes\Grid;
use Poppy\MgrPage\Classes\Grid\Displayer\AbstractDisplayer;
use function request;
use function sys_debug;
use function view;

/**
 * Class Column.
 * @method $this switch ($states = [])
 * @method $this image($server = '', $width = 200, $height = 200)
 * @method $this link($href = '', $target = '_blank')
 * @method $this progress($style = 'primary', $size = 'sm', $max = 100)
 * @method $this downloadable($server = '')
 * @method $this copyable()
 * @method $this qrcode($formatter = null, $width = 150, $height = 150)
 * @method $this prefix($prefix, $delimiter = '&nbsp;')
 * @method $this suffix($suffix, $delimiter = '&nbsp;')
 * @property string $fixed
 * @property string $width
 * @property string $sortable
 * @property string $label
 * @property string $name
 * @property string $style
 * @property string $original
 * @property string $editable
 * @property string $template
 */
class Column
{
    use \Poppy\MgrPage\Classes\Grid\Column\HasHeader;

    const NAME_SELECTOR = '_selector_';
    const NAME_ACTION   = '_actions_';

    /**
     * Displayer for grid column.
     *
     * @var array
     */
    public static $displayers = [
        'switch'       => \Poppy\MgrPage\Classes\Grid\Displayer\SwitchDisplay::class,
        'image'        => \Poppy\MgrPage\Classes\Grid\Displayer\Image::class,
        'link'         => \Poppy\MgrPage\Classes\Grid\Displayer\Link::class,
        'progress'     => \Poppy\MgrPage\Classes\Grid\Displayer\ProgressBar::class,
        'downloadable' => \Poppy\MgrPage\Classes\Grid\Displayer\Downloadable::class,
        'copyable'     => \Poppy\MgrPage\Classes\Grid\Displayer\Copyable::class,
        'qrcode'       => \Poppy\MgrPage\Classes\Grid\Displayer\QRCode::class,
        'prefix'       => \Poppy\MgrPage\Classes\Grid\Displayer\Prefix::class,
        'suffix'       => \Poppy\MgrPage\Classes\Grid\Displayer\Suffix::class,
    ];
    /**
     * Defined columns.
     *
     * @var array
     */
    public static $defined = [];
    /**
     * @var Grid
     */
    protected $grid;
    /**
     * Name of column.
     *
     * @var string
     */
    protected $name;
    /**
     * Label of column.
     *
     * @var string
     */
    protected $label;
    /**
     * Original value of column.
     *
     * @var mixed
     */
    protected $original;
    /**
     * Attributes of column.
     *
     * @var array
     */
    protected $attributes = [];
    /**
     * Relation name.
     *
     * @var bool
     */
    protected $relation = false;
    /**
     * Relation column.
     *
     * @var string
     */
    protected $relationColumn;
    /**
     * @var []Closure
     */
    protected $displayCallbacks = [];
    /**
     * @var bool 是否启用排序
     */
    protected $sortable = false;
    /**
     * @var bool
     */
    protected $searchable = false;

    /**
     * @var string
     */
    protected $style = '';

    /**
     * 定义宽度
     * @var string
     */
    protected $width = 0;
    /**
     * Original grid data.
     *
     * @var Collection
     */
    protected static $originalGridModels;
    /**
     * @var array
     */
    protected static $htmlAttributes = [];
    /**
     * @var array
     */
    protected static $rowAttributes = [];
    /**
     * @var Model
     */
    protected static $model;
    /**
     * @var bool 是否可编辑
     */
    private $editable = false;
    /**
     * 列定位
     * @var string
     */
    private $fixed = '';

    /**
     * @param string $name
     * @param string $label
     */
    public function __construct(string $name, string $label = '')
    {
        $this->name  = $name;
        $this->label = $label ?: ucfirst($name);
    }

    /**
     * Set grid instance for column.
     *
     * @param Grid $grid
     */
    public function setGrid(Grid $grid)
    {
        $this->grid = $grid;

        $this->setModel($grid->model()->eloquent());
    }

    public function editable(): self
    {
        $this->editable = true;
        return $this;
    }

    /**
     * Set model for column.
     *
     * @param $model
     */
    public function setModel($model)
    {
        if (is_null(static::$model) && ($model instanceof BaseModel)) {
            static::$model = $model->newInstance();
        }
    }

    /**
     * Set style of this column.
     *
     * @param string $style
     *
     * @return $this
     */
    public function style(string $style = ''): self
    {
        $this->style = $style . ($this->style ? ';' . $this->style : '');
        return $this;
    }

    /**
     * Set the width of column.
     *
     * @param string|int $width
     *
     * @return $this
     */
    public function width($width): self
    {
        $this->width = $width;
        return $this;
    }

    /**
     * 标识列为可排序
     * @return Column
     */
    public function sortable(): self
    {
        $this->sortable = true;
        return $this;
    }

    /**
     * 标识列为可fix 显示
     * @param string $position
     * @return Column
     */
    public function fixed($position = 'right'): self
    {
        $this->fixed = $position;
        return $this;
    }

    /**
     * Set column filter.
     *
     * @param null $builder
     *
     * @return $this
     */
    public function filter($builder = null)
    {
        return $this->addFilter(...func_get_args());
    }


    /**
     * Set column as searchable.
     *
     * @return $this
     */
    public function searchable()
    {
        $this->searchable = true;

        $name  = $this->name;
        $query = request()->query();

        $this->prefix(function ($_, $original) use ($name, $query) {
            Arr::set($query, $name, $original);

            $url = request()->fullUrlWithQuery($query);

            return "<a href=\"{$url}\"><i class=\"fa fa-search\"></i></a>";
        }, '&nbsp;&nbsp;');

        return $this;
    }

    /**
     * Bind search query to grid model.
     *
     * @param Model $model
     */
    public function bindSearchQuery(Model $model)
    {
        if (!$this->searchable || !request()->has($this->name)) {
            return;
        }

        $model->where($this->name, request($this->name));
    }

    /**
     * Add a display callback.
     *
     * @param Closure $callback
     *
     * @return $this
     */
    public function display(Closure $callback)
    {
        $this->displayCallbacks[] = $callback;

        return $this;
    }

    /**
     * Display using display abstract.
     *
     * @param string $abstract
     * @param array  $arguments
     *
     * @return $this
     */
    public function displayUsing($abstract, $arguments = [])
    {
        $grid = $this->grid;

        $column = $this;

        return $this->display(function ($value) use ($grid, $column, $abstract, $arguments) {
            /** @var AbstractDisplayer $displayer */
            $displayer = new $abstract($value, $grid, $column, $this);

            return $displayer->display(...$arguments);
        });
    }

    /**
     * 替换输出, 并指定默认值, 可以用于状态值替换, 使用KV
     * @param array  $values
     * @param string $default
     * @return $this
     */
    public function using(array $values, $default = ''): self
    {
        return $this->display(function ($value) use ($values, $default) {
            if (is_null($value)) {
                return $default;
            }

            return Arr::get($values, $value, $default);
        });
    }

    /**
     * Render this column with the given view.
     *
     * @param string $view
     *
     * @return $this
     */
    public function view($view)
    {
        return $this->display(function ($value) use ($view) {
            $model = $this;

            return view($view, compact('model', 'value'))->render();
        });
    }

    /**
     * 当前列存在, 但是数据暂时隐藏掉
     * @return $this
     */
    public function hide(): self
    {
        $this->grid->hideColumns($this->name);
        return $this;
    }

    /**
     * Add column to total-row.
     *
     * @param null $display
     *
     * @return $this
     */
    public function totalRow($display = null)
    {
        $this->grid->addTotalRow($this->name, $display);

        return $this;
    }

    /**
     * Convert file size to a human readable format like `100mb`.
     *
     * @return $this
     */
    public function filesize(): self
    {
        return $this->display(function ($value) {
            return UtilHelper::formatBytes($value);
        });
    }

    /**
     * 使用 gravatar 来显示头像图
     * @param int $size
     * @return $this
     */
    public function gravatar($size = 25): self
    {
        return $this->display(function ($value) use ($size) {
            sys_debug('', '', $size);
            $src = sprintf(
                'https://www.gravatar.com/avatar/%s?s=%d',
                md5(strtolower($value)),
                $size
            );
            return "<img src='$src' alt='{$value}' class='img img-circle'/>";
        });
    }

    /**
     * Display field as a loading icon.
     *
     * @param array $values
     * @param array $others
     *
     * @return $this
     */
    public function loading($values = [], $others = [])
    {
        return $this->display(function ($value) use ($values, $others) {
            if (in_array($value, $values)) {
                return '<i class="fa fa-refresh fa-spin text-info"></i>';
            }
            return Arr::get($others, $value, $value);
        });
    }

    /**
     * Return a human readable format time.
     *
     * @param null $locale
     *
     * @return $this
     */
    public function diffForHumans($locale = null): self
    {
        if ($locale) {
            Carbon::setLocale($locale);
        }

        return $this->display(function ($value) {
            return Carbon::parse($value)->diffForHumans();
        });
    }

    /**
     * Returns a string formatted according to the given format string.
     *
     * @param string $format
     *
     * @return $this
     */
    public function date(string $format): self
    {
        return $this->display(function ($value) use ($format) {
            return date($format, strtotime($value));
        });
    }

    /**
     * Display column as boolean , `✓` for true, and `✗` for false.
     *
     * @param array $map
     * @param bool  $default
     *
     * @return $this
     */
    public function bool(array $map = [], $default = false): self
    {
        return $this->display(function ($value) use ($map, $default) {
            $bool = empty($map) ? boolval($value) : Arr::get($map, $value, $default);

            return $bool ? '<i class="fa fa-check text-success"></i>' : '<i class="fa fa-close text-danger"></i>';
        });
    }

    /**
     * Display column using a grid row action.
     *
     * @param string $action
     *
     * @return $this
     */
    public function action($action): self
    {
        if (!is_subclass_of($action, RowAction::class)) {
            throw new InvalidArgumentException("Action class [$action] must be sub-class of [Poppy\System\Classes\Actions\GridAction]");
        }

        $grid = $this->grid;

        return $this->display(function ($_, $column) use ($action, $grid) {
            /** @var RowAction $action */
            $action = new $action();

            return $action
                ->asColumn()
                ->setGrid($grid)
                ->setColumn($column)
                ->setRow($this);
        });
    }

    /**
     * Add a `dot` before column text.
     *
     * @param array  $options
     * @param string $default
     *
     * @return $this
     */
    public function dot($options = [], $default = ''): self
    {
        return $this->prefix(function ($_, $original) use ($options, $default) {
            if (is_null($original)) {
                $style = $default;
            }
            else {
                $style = Arr::get($options, $original);
            }

            return "<span class=\"label-{$style}\" style='width: 8px;height: 8px;padding: 0;border-radius: 50%;display: inline-block;'></span>";
        }, '&nbsp;&nbsp;');
    }

    /**
     * Fill all data to every column.
     *
     * @param array $data
     *
     * @return mixed
     */
    public function fill(array $data)
    {
        foreach ($data as $key => &$row) {
            $this->original = $value = Arr::get($row, $this->name);

            $value = $this->htmlEntityEncode($value);

            Arr::set($row, $this->name, $value);

            if ($this->isDefinedColumn()) {
                $this->useDefinedColumn();
            }

            if ($this->hasDisplayCallbacks()) {
                $value = $this->callDisplayCallbacks($this->original, $key);
                Arr::set($row, $this->name, $value);
            }
        }

        return $data;
    }

    /**
     * Passes through all unknown calls to builtin displayer or supported displayer.
     *
     * Allow fluent calls on the Column object.
     *
     * @param string $method
     * @param array  $arguments
     *
     * @return $this
     */
    public function __call($method, $arguments)
    {
        if ($this->isRelation() && !$this->relationColumn) {
            $this->name  = "{$this->relation}.$method";
            $this->label = ucfirst($arguments[0] ?? null);

            $this->relationColumn = $method;

            return $this;
        }
        return $this->resolveDisplayer($method, $arguments);
    }

    /**
     * 获取类属性
     * @param string $key
     * @return string
     */
    public function __get(string $key)
    {
        return $this->{$key} ?? '';
    }

    /**
     * Extend column displayer.
     *
     * @param $name
     * @param $displayer
     */
    public static function extend($name, $displayer)
    {
        static::$displayers[$name] = $displayer;
    }

    /**
     * Define a column globally.
     *
     * @param string $name
     * @param mixed  $definition
     */
    public static function define($name, $definition)
    {
        static::$defined[$name] = $definition;
    }

    /**
     * 设置列的原始数据
     *
     * @param Collection $collection
     */
    public static function setOriginalGridModels(Collection $collection)
    {
        static::$originalGridModels = $collection;
    }

    /**
     * Get column attributes.
     *
     * @param string $name
     *
     * @return mixed
     */
    public static function getAttributes($name, $key = null)
    {
        $rowAttributes = [];

        if ($key && Arr::has(static::$rowAttributes, "{$name}.{$key}")) {
            $rowAttributes = Arr::get(static::$rowAttributes, "{$name}.{$key}", []);
        }

        $columnAttributes = Arr::get(static::$htmlAttributes, $name, []);

        return array_merge($rowAttributes, $columnAttributes);
    }

    /**
     * If this column is relation column.
     *
     * @return bool
     */
    protected function isRelation()
    {
        return (bool) $this->relation;
    }

    /**
     * Set relation.
     *
     * @param string $relation
     * @param string $relationColumn
     *
     * @return $this
     */
    public function setRelation($relation, $relationColumn = null)
    {
        $this->relation       = $relation;
        $this->relationColumn = $relationColumn;

        return $this;
    }

    /**
     * If has display callbacks.
     *
     * @return bool
     */
    protected function hasDisplayCallbacks()
    {
        return !empty($this->displayCallbacks);
    }

    /**
     * Call all of the "display" callbacks column.
     *
     * @param mixed $value
     * @param int   $key
     *
     * @return mixed
     */
    protected function callDisplayCallbacks($value, $key)
    {
        foreach ($this->displayCallbacks as $callback) {
            $previous = $value;

            $callback = $this->bindOriginalRowModel($callback, $key);
            $value    = call_user_func_array($callback, [$value, $this]);

            if (($value instanceof static) &&
                ($last = array_pop($this->displayCallbacks))
            ) {
                $last  = $this->bindOriginalRowModel($last, $key);
                $value = call_user_func($last, $previous);
            }
        }

        return $value;
    }

    /**
     * Set original grid data to column.
     *
     * @param Closure $callback
     * @param int     $key
     *
     * @return Closure
     */
    protected function bindOriginalRowModel(Closure $callback, $key)
    {
        $rowModel = static::$originalGridModels[$key];

        return $callback->bindTo($rowModel);
    }

    /**
     * If current column is a defined column.
     *
     * @return bool
     */
    protected function isDefinedColumn()
    {
        return array_key_exists($this->name, static::$defined);
    }

    /**
     * Use a defined column.
     *
     * @throws Exception
     */
    protected function useDefinedColumn()
    {
        // clear all display callbacks.
        $this->displayCallbacks = [];

        $class = static::$defined[$this->name];

        if ($class instanceof Closure) {
            $this->display($class);

            return;
        }

        if (!class_exists($class) || !is_subclass_of($class, AbstractDisplayer::class)) {
            throw new Exception("Invalid column definition [$class]");
        }

        $grid   = $this->grid;
        $column = $this;

        $this->display(function ($value) use ($grid, $column, $class) {
            /** @var AbstractDisplayer $definition */
            $definition = new $class($value, $grid, $column, $this);

            return $definition->display();
        });
    }

    /**
     * Convert characters to HTML entities recursively.
     *
     * @param array|string $item
     *
     * @return mixed
     */
    protected function htmlEntityEncode($item)
    {
        if (is_array($item)) {
            array_walk_recursive($item, function (&$value) {
                $value = htmlentities($value);
            });
        }
        else {
            $item = htmlentities($item);
        }

        return $item;
    }

    /**
     * Find a displayer to display column.
     *
     * @param string $method
     * @param array  $arguments
     *
     * @return $this
     */
    protected function resolveDisplayer(string $method, array $arguments): self
    {
        if (array_key_exists($method, static::$displayers)) {
            return $this->callBuiltinDisplayer(static::$displayers[$method], $arguments);
        }
        return $this->callSupportDisplayer($method, $arguments);
    }

    /**
     * Call Illuminate/Support displayer.
     *
     * @param string $method
     * @param array  $arguments
     * @return $this
     */
    protected function callSupportDisplayer(string $method, array $arguments): self
    {
        return $this->display(function ($value) use ($method, $arguments) {
            if (is_array($value) || $value instanceof Arrayable) {
                return call_user_func_array([collect($value), $method], $arguments);
            }

            if (is_string($value)) {
                return call_user_func_array([Str::class, $method], array_merge([$value], $arguments));
            }

            return $value;
        });
    }

    /**
     * Call Builtin displayer.
     *
     * @param string $abstract
     * @param array  $arguments
     *
     * @return $this
     */
    protected function callBuiltinDisplayer(string $abstract, array $arguments): self
    {
        if ($abstract instanceof Closure) {
            return $this->display(function ($value) use ($abstract, $arguments) {
                return $abstract->call($this, ...array_merge([$value], $arguments));
            });
        }

        if (class_exists($abstract) && is_subclass_of($abstract, AbstractDisplayer::class)) {
            $grid   = $this->grid;
            $column = $this;

            return $this->display(function ($value) use ($abstract, $grid, $column, $arguments) {
                /** @var AbstractDisplayer $displayer */
                $displayer = new $abstract($value, $grid, $column, $this);

                return $displayer->display(...$arguments);
            });
        }

        return $this;
    }
}
