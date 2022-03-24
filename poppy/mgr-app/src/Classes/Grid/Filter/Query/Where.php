<?php

namespace Poppy\MgrApp\Classes\Grid\Filter\Query;

use Closure;
use Illuminate\Support\Arr;
use Poppy\MgrApp\Classes\Form\Traits\UseOptions;
use Poppy\MgrApp\Classes\Form\Traits\UsePlaceholder;
use Poppy\MgrApp\Classes\Grid\Filter\Traits\AsBetween;
use Poppy\MgrApp\Classes\Grid\Filter\Traits\AsDatetime;
use Poppy\MgrApp\Classes\Grid\Filter\Traits\AsMultiSelect;
use Poppy\MgrApp\Classes\Grid\Filter\Traits\AsSelect;
use Poppy\MgrApp\Classes\Grid\Filter\Traits\AsText;
use ReflectionException;
use ReflectionFunction;

class Where extends FilterItem
{

    use UsePlaceholder, UseOptions, AsSelect, AsDatetime, AsText, AsMultiSelect, AsBetween;

    /**
     * Query closure.
     *
     * @var Closure
     */
    protected Closure $where;

    /**
     * Where constructor.
     *
     * @param Closure     $query
     * @param string      $label
     * @param string|null $column
     * @throws ReflectionException
     */
    public function __construct(Closure $query, string $label, string $column = null)
    {
        $this->where = $query;
        if (!$column) {
            $column = static::getQueryHash($query, $label);
        }
        parent::__construct($column, $label);
    }

    /**
     * Get the hash string of query closure.
     *
     * @param Closure $closure
     * @param string  $label
     *
     * @return string
     * @throws ReflectionException
     */
    public static function getQueryHash(Closure $closure, string $label = ''): string
    {
        $reflection = new ReflectionFunction($closure);
        return md5($reflection->getFileName() . $reflection->getStartLine() . $reflection->getEndLine() . $label);
    }

    /**
     * Get condition of this filter.
     *
     * @param array $inputs
     *
     * @return array|mixed|void
     * @throws ReflectionException
     */
    public function condition(array $inputs)
    {
        $value = Arr::get($inputs, $this->name ?: static::getQueryHash($this->where, $this->label));

        if (is_null($value)) {
            return;
        }

        $this->value = $value;

        return $this->buildCondition($this->where->bindTo($this));
    }
}
