<?php

namespace Poppy\MgrApp\Classes\Grid\Query;

use Closure;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Poppy\Framework\Exceptions\ApplicationException;
use Poppy\Framework\Helper\UtilHelper;
use Poppy\MgrApp\Classes\Grid\Column\Column;
use Poppy\MgrApp\Classes\Widgets\FilterWidget;
use Poppy\MgrApp\Classes\Widgets\TableWidget;
use function collect;
use function request;

class QueryModel extends Query
{

    /**
     * Eloquent model instance of the grid model.
     *
     * @var Model | Builder |QueryBuilder
     */
    protected $model;

    /**
     * @var Model
     */
    protected Model $origin;

    /**
     * Array of queries of the eloquent model.
     *
     * @var Collection
     */
    protected Collection $queries;

    /**
     * Sort parameters of the model.
     *
     * @var array|string
     */
    protected $sort;


    /**
     * @var ?Relation
     */
    protected ?Relation $relation = null;

    /**
     * @var array
     */
    protected array $eagerLoads = [];

    /**
     * Create a new grid model instance.
     *
     * @param Model $model
     */
    public function __construct(Model $model)
    {
        $this->model = $model;

        $this->origin = $model;

        $this->queries = collect();
    }

    /**
     * 启用或者禁用分页
     * @param bool $paginate
     */
    public function usePaginate(bool $paginate = true)
    {
        $this->usePaginate = $paginate;
    }

    /**
     * 组建数据
     * @throws Exception
     */
    public function get(): Collection
    {
        $collection = $this->fetchData();

        if ($this->collectionCallback) {
            $collection = call_user_func($this->collectionCallback, $collection);
        }

        return $collection;
    }

    /**
     * @param Closure $closure
     * @param int $amount
     * @return Collection|bool
     * @throws Exception
     */
    public function chunk(Closure $closure, int $amount = 100)
    {
        if ($this->usePaginate) {
            return $this->get()->chunk($amount)->each($closure);
        }

        $this->setSort();

        $this->queries->reject(function ($query) {
            return $query['method'] == 'paginate';
        })->each(function ($query) {
            $this->model = $this->model->{$query['method']}(...$query['arguments']);
        });

        return $this->model->chunk($amount, $closure);
    }

    /**
     * 查询条件预取
     * @param FilterWidget $filter
     * @param TableWidget $table
     * @return $this
     * @throws ApplicationException
     */
    public function prepare(FilterWidget $filter, TableWidget $table): self
    {
        $this->addConditions($filter->conditions())->prepareTable($table);
        return $this;
    }

    /**
     * @param mixed $id
     * @param string $field
     * @param mixed $value
     * @return bool
     */
    public function edit($id, string $field, $value): bool
    {
        $pk = $this->origin->getKeyName();
        if (!$pk) {
            return false;
        }
        $this->origin->where($pk, $id)->update([
            $field => $value,
        ]);
        return true;
    }

    public function total(): int
    {
        return $this->total;
    }

    /**
     * @return string
     */
    public function getPrimaryKey(): string
    {
        return $this->origin->getKeyName();
    }

    /**
     * @param array $ids
     * @return mixed
     */
    public function usePrimaryKey(array $ids = []): self
    {
        $this->__call('whereIn', [$this->getPrimaryKey(), $ids]);
        return $this;
    }

    /**
     * 调用查询方法并把参数放入到查询条件中
     * @param string $method
     * @param array $arguments
     * @return $this
     */
    public function __call(string $method, array $arguments)
    {
        $this->queries->push([
            'method'    => $method,
            'arguments' => $arguments,
        ]);
        return $this;
    }

    /**
     * @return Collection
     * @throws Exception
     */
    protected function fetchData()
    {
        if ($this->relation) {
            $this->model = $this->relation->getQuery();
        }

        $this->setSort();

        $this->setPaginate();

        // 这里存在分页, 默认则返回
        $this->queries->unique()->each(function ($query) {
            $this->model = call_user_func_array([$this->model, $query['method']], $query['arguments']);
        });

        if ($this->model instanceof LengthAwarePaginator) {
            $this->total = $this->model->total();
            return $this->model->getCollection();
        }

        throw new ApplicationException('当前查询方式不支持非指定数据查询');
    }

    /**
     * 设置分页
     * @return void
     */
    protected function setPaginate()
    {
        // [paginate, [15]]
        $paginate = $this->findQueryByMethod('paginate');

        // 从集合中删除分页方法
        $this->queries = $this->queries->reject(function ($query) {
            return $query['method'] == 'paginate';
        });

        if ($this->usePaginate) {
            // 组合分页条件
            $query = [
                'method'    => 'paginate',
                'arguments' => $this->resolvePagesize($paginate),
            ];

            $this->queries->push($query);
        }
    }

    /**
     * Resolve pagesize for pagination.
     *
     * @param array|null $paginate
     * @return array
     */
    protected function resolvePagesize($paginate)
    {
        if ($pagesize = request(self::NAME_PAGESIZE)) {
            if (is_array($paginate)) {
                $paginate['arguments'][0] = (int) $pagesize;

                return $paginate['arguments'];
            }

            $this->pagesize = (int) $pagesize;
        }

        if (isset($paginate['arguments'][0])) {
            return $paginate['arguments'];
        }

        return [$this->pagesize];
    }

    /**
     * 通过方法名称查找组合模型的条件
     * @param string $method
     * @return array
     *              method : paginate
     *              arguments  : [15]
     */
    protected function findQueryByMethod(string $method): ?array
    {
        return $this->queries->first(function ($query) use ($method) {
            return $query['method'] == $method;
        });
    }

    /**
     * 设置排序
     * _sort {
     *    column :
     *    type :
     *    cast :
     * }
     * @return void
     */
    protected function setSort()
    {
        $this->sort = request(TableWidget::NAME_SORT, []);

        if (!is_array($this->sort)) {

            if (!Str::startsWith($this->sort, self::OBJECT_MASK)) {
                return;
            }
            $sortEncode = base64_decode(Str::after($this->sort, self::OBJECT_MASK));
            if (!UtilHelper::isJson($sortEncode)) {
                return;
            }
            $this->sort = json_decode($sortEncode, true);
        }

        if (empty($this->sort['column']) || empty($this->sort['type'])) {
            return;
        }

        if (Str::contains($this->sort['column'], '.')) {
            $this->setRelationSort($this->sort['column']);
        } else {
            $this->resetOrderBy();
            $column    = $this->sort['column'];
            $method    = 'orderBy';
            $arguments = [$column, $this->sort['type']];
            $this->queries->push([
                'method'    => $method,
                'arguments' => $arguments,
            ]);
        }
    }

    /**
     * Set relation sort.
     *
     * @param string $column
     *
     * @return void
     * @throws Exception
     */
    protected function setRelationSort(string $column)
    {
        [$relationName, $relationColumn] = explode('.', $column);

        if ($this->queries->contains(function ($query) use ($relationName) {
            return $query['method'] == 'with' && in_array($relationName, $query['arguments']);
        })) {
            $relation = $this->model->$relationName();

            $this->queries->push([
                'method'    => 'select',
                'arguments' => [$this->model->getTable() . '.*'],
            ]);

            $this->queries->push([
                'method'    => 'join',
                'arguments' => $this->joinParameters($relation),
            ]);

            $this->resetOrderBy();

            $this->queries->push([
                'method'    => 'orderBy',
                'arguments' => [
                    $relation->getRelated()->getTable() . '.' . $relationColumn,
                    $this->sort['type'],
                ],
            ]);
        }
    }

    /**
     * Build join parameters for related model.
     *
     * `HasOne` and `BelongsTo` relation has different join parameters.
     *
     * @param Relation $relation
     *
     * @return ?array
     * @throws Exception
     *
     */
    protected function joinParameters(Relation $relation): ?array
    {
        $relatedTable = $relation->getRelated()->getTable();

        if ($relation instanceof BelongsTo) {
            $foreignKeyMethod = 'getForeignKeyName';

            return [
                $relatedTable,
                $relation->{$foreignKeyMethod}(),
                '=',
                $relatedTable . '.' . $relation->getRelated()->getKeyName(),
            ];
        }

        if ($relation instanceof HasOne) {
            return [
                $relatedTable,
                $relation->getQualifiedParentKeyName(),
                '=',
                $relation->getQualifiedForeignKeyName(),
            ];
        }

        throw new Exception('Related sortable only support `HasOne` and `BelongsTo` relation.');
    }

    /**
     * Reset orderBy query.
     *
     * @return void
     */
    private function resetOrderBy()
    {
        $this->queries = $this->queries->reject(function ($query) {
            return $query['method'] == 'orderBy' || $query['method'] == 'orderByDesc';
        });
    }

    /**
     * 添加条件到 Model
     * @param array $conditions
     * @return $this
     */
    private function addConditions(array $conditions): self
    {
        foreach ($conditions as $condition) {
            // [$this, where][title, like, '%我%']
            call_user_func_array([$this, key($condition)], current($condition));
        }

        return $this;
    }

    /**
     * 设置渴望加载的关系数据
     * @param mixed $relations
     * @return $this
     */
    private function with($relations)
    {
        if (is_array($relations)) {
            if (Arr::isAssoc($relations)) {
                $relations = array_keys($relations);
            }

            $this->eagerLoads = array_merge($this->eagerLoads, $relations);
        }

        if (is_string($relations)) {
            if (Str::contains($relations, '.')) {
                $relations = explode('.', $relations)[0];
            }

            if (Str::contains($relations, ':')) {
                $relations = explode(':', $relations)[0];
            }

            if (in_array($relations, $this->eagerLoads)) {
                return $this;
            }

            $this->eagerLoads[] = $relations;
        }

        return $this->__call('with', (array) $relations);
    }

    /**
     * 检查定义并加入 with 查询
     * @param TableWidget $table
     * @throws ApplicationException
     */
    private function prepareTable(TableWidget $table)
    {
        // 验证添加 relation
        // mutator | json 可以使用 data_get 从对象中获取数据
        $table->columns->each(function (Column $column) {
            $method = $column->relation;
            if (!$method) {
                return;
            }
            $class = get_class($this->model);
            if (!method_exists($this->model, $method)) {
                throw new ApplicationException("Call to undefined relationship [{$method}] on model [{$class}].");
            }
            // relation

            if (!($relation = $this->model->$method()) instanceof Relation) {
                return;
            }

            if ($relation instanceof HasOne ||
                $relation instanceof BelongsTo ||
                $relation instanceof MorphOne
            ) {
                if (!$column->relationMany) {
                    $this->with($method);
                } else {
                    throw new ApplicationException("Relationship [{$method}] on model [{$class}] is Many.");
                }
            }

            if ($relation instanceof HasMany || $relation instanceof HasManyThrough || $relation instanceof BelongsToMany) {
                if ($column->relationMany) {
                    $this->with($method);
                } else {
                    throw new ApplicationException("Relationship [{$method}] on model [{$class}] is One To One.");
                }
            }
        });
    }
}
