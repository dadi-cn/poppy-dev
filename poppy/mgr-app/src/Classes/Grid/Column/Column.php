<?php

namespace Poppy\MgrApp\Classes\Grid\Column;

use Carbon\Carbon;
use Closure;
use Exception;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Poppy\Framework\Helper\UtilHelper;
use Poppy\MgrApp\Classes\Contracts\Structable;
use Poppy\MgrApp\Classes\Grid\Column\Render\ActionsRender;
use Poppy\MgrApp\Classes\Grid\Column\Render\DownloadRender;
use Poppy\MgrApp\Classes\Grid\Column\Render\EditableRender;
use Poppy\MgrApp\Classes\Grid\Column\Render\HiddenRender;
use Poppy\MgrApp\Classes\Grid\Column\Render\ImageRender;
use Poppy\MgrApp\Classes\Grid\Column\Render\LinkRender;
use Poppy\MgrApp\Classes\Grid\Column\Render\Render;

/**
 * 列展示以及渲染, 当前的目的是使用前端方式渲染, 而不是依靠于 v-html 或者是后端生成
 * @property-read string $name        当前列的名称
 * @property-read string $relation    当前关系
 * @property-read bool $relationMany  是否是一对多关系
 * @property-read string $label       标签
 * @method Column image($server = '', $width = 200, $height = 200)   // todo 需要完成图片的缩略图约定
 * @method Column link($href = '', $target = '_blank')
 * @method Column editable(Closure $callback = null, string $query = '', string $field = '')    将文本渲染为 Text 可编辑模式
 * @method Column switchable()                    将文本渲染为可切换的开关模式
 * @method Column download($server = '')
 * @method Column hidden(Closure $callback = null, string $query = '', string $field = '') 隐藏数据并支持自定义查询
 */
class Column implements Structable
{
    use HasHeader;

    public const NAME_ACTION = '_action';     // 用于定义列操作, 可以在导出时候移除

    /**
     * renders for grid column.
     *
     * @var array
     */
    public static array $renderers = [
        'image'    => ImageRender::class,
        'link'     => LinkRender::class,
        'hidden'   => HiddenRender::class,
        'download' => DownloadRender::class,
        'editable' => EditableRender::class,
    ];

    /**
     * Defined columns.
     *
     * @var array
     */
    public static $defined = [];

    /**
     * 列名称
     * @var string
     */
    protected string $name;

    /**
     * Label of column.
     *
     * @var string
     */
    protected string $label = '';

    /**
     * Original value of column.
     *
     * @var mixed
     */
    protected $original;

    /**
     * Relation 的名称
     * @var string
     */
    protected string $relation = '';

    /**
     * 关系列
     * @var string
     */
    protected string $relationColumn = '';

    /**
     * @var []Closure
     */
    protected $renderCallbacks = [];

    /**
     * @var bool 是否启用排序
     */
    protected bool $sortable = false;

    /**
     * @var bool 是否进行文字隐藏展示
     */
    protected bool $ellipsis = false;

    /**
     * 可复制的
     * @var bool
     */
    protected bool $copyable = false;

    /**
     * todo 可搜索的
     * @var bool
     */
    protected bool $searchable = false;

    /**
     * 定义宽度
     * @var int
     */
    protected int $width = 0;

    /**
     * 对齐方式
     * @var string
     */
    protected string $align = '';

    /**
     * 渲染类型
     * @var string
     */
    private string $type = 'text';

    /**
     * 列固定
     * @var string
     */
    private string $fixed = '';

    /**
     * 最小宽度
     * @var int
     */
    private int $minWidth = 150;

    /**
     * 是否是 一对多 关系
     * @var bool
     */
    private bool $relationMany = false;


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
     * 渲染为ID
     * @return $this
     */
    public function quickId($large = false): self
    {
        $width = $large ? 110 : 90;
        $this->width($width, true)->align('center');
        return $this;
    }

    /**
     * 渲染为标题, 默认显示 15个汉字, large 模式显示 20个汉字左右
     * @return $this
     */
    public function quickTitle($large = false): self
    {
        $width = $large ? 320 : 250;
        $this->ellipsis()->width($width, true)->copyable();
        return $this;
    }

    /**
     * 渲染为 Datetime 时间
     * @return $this
     */
    public function quickDatetime(): self
    {
        $this->width(170, true)->align('center');
        return $this;
    }

    /**
     * 定义快捷样式
     * @return $this
     */
    public function quickIcon($num = 3, $fixed = true): self
    {
        $width = 16 + $num * 44;
        $this->width($width, true)->align('center');
        if ($fixed) {
            $this->fixed();
        }
        return $this;
    }

    /**
     * 设置列宽度, 单个按钮 最优宽度 60(图标), 每个按钮增加 45 宽度
     * Datetime 最优宽度 170
     * @param int $width  宽度
     * @param bool $fixed 是否是固定宽度
     * @return $this
     */
    public function width(int $width, bool $fixed = false): self
    {
        if ($fixed) {
            $this->width = $width;
        }
        else {
            $this->minWidth = $width;
        }
        return $this;
    }

    /**
     * 设置展示位置, 默认 left, 可选 [left,center,right]
     * @param string|int $align
     * @return $this
     */
    public function align($align): self
    {
        $this->align = $align;
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
     * 隐藏字符展示
     * @return Column
     */
    public function ellipsis(): self
    {
        $this->ellipsis = true;
        return $this;
    }

    /**
     * 可复制
     * @return Column
     */
    public function copyable(): self
    {
        $this->copyable = true;
        return $this;
    }


    /**
     * 标识列为可fix 显示, 默认是右侧, 可以设置为 [left, right]
     * @param string $position
     * @return Column
     */
    public function fixed(string $position = 'right'): self
    {
        $this->fixed = $position;
        return $this;
    }

    /**
     * 列搜索工具
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
    public function searchable(): self
    {
        $this->searchable = true;
        return $this;
    }

    public function setType($type)
    {
        $this->type = $type;
    }

    /**
     * 设置 Name 值
     * @param $name
     * @return $this
     */
    public function setName($name): self
    {
        $this->name = $name;
        return $this;
    }

    public function struct(): array
    {
        $defines = [
            'field' => $this->name,
            'label' => $this->label,
            'type'  => $this->type,
        ];

        if ($this->sortable) {
            $defines += ['sortable' => 'custom'];
        }
        if ($this->copyable) {
            $defines += ['copyable' => $this->copyable];
        }
        if ($this->ellipsis) {
            $defines += ['ellipsis' => $this->ellipsis];
        }
        if ($this->width) {
            $defines += ['width' => $this->width];
        }
        if ($this->align) {
            $defines += ['align' => $this->align];
        }
        if ($this->minWidth) {
            $defines += ['min-width' => $this->minWidth];
        }
        if ($this->fixed) {
            $defines += ['fixed' => $this->fixed];
        }
        return $defines;
    }

    public function html(Closure $closure): self
    {
        $this->type = 'html';
        return $this->display($closure);
    }

    /**
     * 定义回调
     * @param Closure $callback
     * @return $this
     */
    public function display(Closure $callback): self
    {
        $this->renderCallbacks[] = $callback;
        return $this;
    }


    /**
     * 调用 Actions 预览定义的操作
     * @return $this
     */
    public function actions(Closure $closure): self
    {
        $column = $this;
        $name   = $this->name;
        return $this->display(function ($value) use ($column, $closure, $name) {
            $render = new ActionsRender($value, $this, $name);
            $column->setType('actions');
            return $render->render($closure);
        });
    }

    /**
     * 使用KV进行替换输出, 并可以指定默认值
     * @param array $values
     * @param string $default
     * @return $this
     */
    public function usingKv(array $values, string $default = ''): self
    {
        return $this->display(function ($value) use ($values, $default) {
            if (is_null($value)) {
                return $default;
            }

            return Arr::get($values, $value, $default);
        });
    }


    /**
     * 显示为友好的文件大小
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
            $src = sprintf(
                'https://www.gravatar.com/avatar/%s?s=%d',
                md5(strtolower($value)),
                $size
            );
            return "<img src='$src' alt='{$value}' class='img img-circle'/>";
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
     * @throws Exception
     */
    public function fillVal($row)
    {
        if ($this->relationMany) {
            [$relation, $field] = explode(':', $this->name);
            $relations = data_get($row, $relation);
            if ($relations instanceof Collection) {
                $value = $relations->pluck($field);
            }
            else {
                $value = $relations;
            }
        }
        else {
            $value = Arr::get($row, $this->name);
            if ($value instanceof Carbon) {
                $value = $value->toDateTimeString();
            }
        }
        if ($this->isDefinedColumn()) {
            $this->useDefinedColumn();
        }

        if ($this->hasRenderCallbacks()) {
            $value = $this->callDisplayCallbacks($value, $row);
        }
        return $value;
    }

    /**
     * Passes through all unknown calls to builtin renders or supported render.
     *
     * Allow fluent calls on the Column object.
     *
     * @param string $method
     * @param array $arguments
     *
     * @return $this
     */
    public function __call(string $method, array $arguments)
    {
        if ($this->isRelation() && !$this->relationColumn) {
            $this->name  = "{$this->relation}.$method";
            $this->label = ucfirst($arguments[0] ?? null);

            $this->relationColumn = $method;

            return $this;
        }
        return $this->resolveRender($method, $arguments);
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
     * Extend column render.
     *
     * @param $name
     * @param $render
     */
    public static function extend($name, $render)
    {
        static::$renderers[$name] = $render;
    }

    /**
     * 定义全局列渲染
     * @param string $name
     * @param mixed $definition
     */
    public static function define($name, $definition)
    {
        static::$defined[$name] = $definition;
    }


    /**
     * 设置 Relation
     * @param string $relation
     * @param string $field
     * @param bool $many
     * @return $this
     */
    public function setRelation(string $relation, string $field, bool $many = false): self
    {
        $this->relation       = $relation;
        $this->relationColumn = $field;
        $this->relationMany   = $many;
        return $this;
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
     * If has display callbacks.
     *
     * @return bool
     */
    protected function hasRenderCallbacks()
    {
        return !empty($this->renderCallbacks);
    }

    /**
     * 调用所有的列渲染回调, 因为 row 可能未自定义的 Query 渲染, 所以返回可能是数组, 这里使用 collect 包裹处理
     * @param mixed $value
     * @param $row
     * @return mixed
     */
    protected function callDisplayCallbacks($value, $row)
    {
        if (is_array($row)) {
            $row = collect($row);
        }
        foreach ($this->renderCallbacks as $callback) {
            $previous = $value;

            $callback = $callback->bindTo($row);

            $value = call_user_func_array($callback, [$value, $this]);

            if (($value instanceof static) &&
                ($last = array_pop($this->renderCallbacks))
            ) {
                $last  = $last->bindTo($row);
                $value = call_user_func($last, $previous);
            }
        }

        return $value;
    }

    /**
     * 当前列是否在全局定义中
     * @return bool
     */
    protected function isDefinedColumn(): bool
    {
        return array_key_exists($this->name, static::$defined);
    }

    /**
     * 使用全局列定义
     * @throws Exception
     */
    protected function useDefinedColumn()
    {
        // clear all display callbacks.
        $this->renderCallbacks = [];

        $class = static::$defined[$this->name];

        if ($class instanceof Closure) {
            $this->display($class);
            return;
        }

        if (!class_exists($class) || !is_subclass_of($class, Render::class)) {
            throw new Exception("Invalid column definition [$class]");
        }

        $column = $this;

        $this->display(function ($value) use ($column, $class) {
            /** @var Render $render */
            $render       = new $class($value, $this);
            $column->type = $render->getType();
            return $render->render();
        });
    }

    /**
     * Find a render to display column.
     *
     * @param string $method
     * @param array $arguments
     *
     * @return $this
     */
    protected function resolveRender(string $method, array $arguments): self
    {
        $this->type = $method;
        if (array_key_exists($method, static::$renderers)) {
            return $this->callBuiltinRender(static::$renderers[$method], $arguments);
        }
        return $this->callSupportRender($method, $arguments);
    }

    /**
     * Call Illuminate/Support.
     *
     * @param string $method
     * @param array $arguments
     * @return $this
     */
    protected function callSupportRender(string $method, array $arguments): self
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
     * Call Builtin.
     *
     * @param string|Closure $abstract
     * @param array $arguments
     *
     * @return $this
     */
    protected function callBuiltinRender($abstract, array $arguments): self
    {
        if ($abstract instanceof Closure) {
            return $this->display(function ($value) use ($abstract, $arguments) {
                return $abstract->call($this, ...array_merge([$value], $arguments));
            });
        }

        if (class_exists($abstract) && is_subclass_of($abstract, Render::class)) {
            $column = $this;
            $name   = $this->name;
            return $this->display(function ($value) use ($abstract, $column, $arguments, $name) {
                /** @var Render $render */
                $render       = new $abstract($value, $this, $name);
                $column->type = $render->getType();
                return $render->render(...$arguments);
            });
        }
        return $this;
    }
}
