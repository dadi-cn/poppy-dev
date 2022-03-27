<?php

namespace Poppy\MgrPage\Classes\Form\Field;

use Exception;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Arr;

class MultipleSelect extends Select
{
    /**
     * Other key for many-to-many relation.
     *
     * @var string
     */
    protected $otherKey;

    /**
     * @inheritDoc
     */
    public function fill($data)
    {
        $relations = Arr::get($data, $this->column);

        if (is_string($relations)) {
            $this->value = explode(',', $relations);
        }

        if (!is_array($relations)) {
            return;
        }

        $first = current($relations);

        if (is_null($first)) {
            $this->value = null;

            // MultipleSelect value store as an ont-to-many relationship.
        }
        elseif (is_array($first)) {
            foreach ($relations as $relation) {
                $this->value[] = Arr::get($relation, "pivot.{$this->getOtherKey()}");
            }

            // MultipleSelect value store as a column.
        }
        else {
            $this->value = $relations;
        }
    }

    /**
     * @inheritDoc
     */
    public function setOriginal($data)
    {
        $relations = Arr::get($data, $this->column);

        if (is_string($relations)) {
            $this->original = explode(',', $relations);
        }

        if (!is_array($relations)) {
            return;
        }

        $first = current($relations);

        if (is_null($first)) {
            $this->original = null;

            // MultipleSelect value store as an ont-to-many relationship.
        }
        elseif (is_array($first)) {
            foreach ($relations as $relation) {
                $this->original[] = Arr::get($relation, "pivot.{$this->getOtherKey()}");
            }

            // MultipleSelect value store as a column.
        }
        else {
            $this->original = $relations;
        }
    }

    public function prepare($value)
    {
        $value = (array) $value;

        return array_filter($value, 'strlen');
    }

    /**
     * Get other key for this many-to-many relation.
     *
     * @return string
     * @throws Exception
     *
     */
    protected function getOtherKey()
    {
        if ($this->otherKey) {
            return $this->otherKey;
        }

        if (is_callable([$this->form->model(), $this->column]) &&
            ($relation = $this->form->model()->{$this->column}()) instanceof BelongsToMany
        ) {
            /* @var BelongsToMany $relation */
            $fullKey      = $relation->getQualifiedRelatedPivotKeyName();
            $fullKeyArray = explode('.', $fullKey);

            return $this->otherKey = end($fullKeyArray);
        }

        throw new Exception('Column of this field must be a `BelongsToMany` relation.');
    }
}
