<?php

namespace Poppy\MgrPage\Classes\Grid;

use Closure;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Illuminate\Support\Traits\Macroable;
use Poppy\Framework\Exceptions\ApplicationException;
use Poppy\MgrPage\Classes\Grid;
use Poppy\System\Models\PamAccount;
use function app;
use function collect;
use function tap;

abstract class ListBase implements ListContract
{
    use Macroable {
        __call as macroCall;
    }


    public $title = '';

    /**
     * @var Grid
     */
    protected $grid;


    /**
     * Collection of all grid columns.
     *
     * @var Collection
     */
    protected $columns;

    /**
     * @var PamAccount
     */
    protected $pam;

    public function __construct(Grid $grid)
    {
        $this->pam     = app('auth')->user();
        $this->grid    = $grid;
        $this->columns = collect();
    }


    /**
     * Add a column to Grid.
     *
     * @param string $name
     * @param string $label
     *
     * @return Column
     * @throws ApplicationException
     */
    public function column(string $name, $label = '')
    {
        if (Str::contains($name, '.')) {
            return $this->addRelationColumn($name, $label);
        }

        if (Str::contains($name, '->')) {
            return $this->addJsonColumn($name, $label);
        }

        return $this->__call($name, array_filter([$label]));
    }

    /**
     * Dynamically add columns to the grid view.
     *
     * @param string $method
     * @param array  $parameters
     *
     * @return Column
     */
    public function __call(string $method, array $parameters)
    {
        if (static::hasMacro($method)) {
            return $this->macroCall($method, $parameters);
        }

        $label = $parameters[0] ?? '';

        if ($this->model()->eloquent()) {
            return $this->addColumn($method, $label);
        }

        if ($column = $this->handleGetMutatorColumn($method, $label)) {
            return $column;
        }

        if ($column = $this->handleRelationColumn($method, $label)) {
            return $column;
        }

        return $this->addColumn($method, $label);
    }

    /**
     * @return Collection
     */
    public function getColumns(): Collection
    {
        return $this->columns;
    }

    public function filter(): Closure
    {
        return function (Filter $filter) {
        };
    }

    public function actions()
    {

    }

    public function quickButtons(): array
    {
        return [];
    }

    public function batchAction(): array
    {
        return [];
    }

    /**
     * Add a relation column to grid.
     *
     * @param string $name
     * @param string $label
     * @return $this|bool|Column
     * @throws ApplicationException
     */
    protected function addRelationColumn($name, $label = '')
    {
        [$relation, $column] = explode('.', $name);

        $model = $this->model()->eloquent();

        if (!method_exists($model, $relation) || !$model->{$relation}() instanceof Relation) {
            $class = get_class($model);
            throw new ApplicationException("Call to undefined relationship [{$relation}] on model [{$class}].");
        }

        $name = Str::snake($relation) . '.' . $column;

        $this->model()->with($relation);

        return $this->addColumn($name, $label)->setRelation($relation, $column);
    }

    /**
     * Add a json type column to grid.
     *
     * @param string $name
     * @param string $label
     *
     * @return Column
     */
    protected function addJsonColumn($name, $label = '')
    {
        $column = substr($name, strrpos($name, '->') + 2);

        $name = str_replace('->', '.', $name);

        return $this->addColumn($name, $label ?: ucfirst($column));
    }

    /**
     * Prepend column to grid.
     *
     * @param string $column
     * @param string $label
     *
     * @return Column
     */
    protected function prependColumn($column = '', $label = '')
    {
        $column = new Column($column, $label);
        $column->setGrid($this->grid);

        return tap($column, function ($value) {
            $this->columns->prepend($value);
        });
    }

    /**
     * Add column to grid.
     *
     * @param string $column
     * @param string $label
     *
     * @return Column
     */
    protected function addColumn($column = '', $label = '')
    {
        $column = new Column($column, $label);
        $column->setGrid($this->grid);

        return tap($column, function ($value) {
            $this->columns->push($value);
        });
    }

    /**
     * Handle get mutator column for grid.
     *
     * @param string $method
     * @param string $label
     *
     * @return bool|Column
     */
    protected function handleGetMutatorColumn($method, $label)
    {
        if ($this->model()->eloquent()->hasGetMutator($method)) {
            return $this->addColumn($method, $label);
        }

        return false;
    }

    /**
     * Handle relation column for grid.
     *
     * @param string $method
     * @param string $label
     *
     * @return bool|Column
     */
    protected function handleRelationColumn($method, $label)
    {
        $model = $this->model()->eloquent();

        if (!method_exists($model, $method)) {
            return false;
        }

        if (!($relation = $model->$method()) instanceof Relation) {
            return false;
        }

        if ($relation instanceof HasOne ||
            $relation instanceof BelongsTo ||
            $relation instanceof MorphOne
        ) {
            $this->model()->with($method);
            return $this->addColumn($method, $label)->setRelation(Str::snake($method));
        }

        if ($relation instanceof HasMany
            || $relation instanceof BelongsToMany
            || $relation instanceof MorphToMany
            || $relation instanceof HasManyThrough
        ) {
            $this->model()->with($method);

            return $this->addColumn(Str::snake($method), $label);
        }

        return false;
    }

    /**
     * 当前的数据模型
     * @return Model|\Poppy\MgrPage\Classes\Grid\Model
     */
    private function model()
    {
        return $this->grid->model();
    }
}
