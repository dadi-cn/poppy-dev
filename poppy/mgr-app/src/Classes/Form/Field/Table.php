<?php

namespace Poppy\MgrApp\Classes\Form\Field;

use Closure;
use Poppy\MgrApp\Classes\Form\FormItem;
use Poppy\MgrApp\Classes\Table\TablePlugin;

class Table extends FormItem
{
    protected TablePlugin $table;

    public function __construct(string $name, string $label)
    {
        parent::__construct($name, $label);
        $this->table = new TablePlugin();
    }

    public function cols(Closure $closure)
    {
        $closure->call($this, $this->table);
        $this->setAttribute('cols', $this->table->struct());
    }

    /**
     * 获取定义的表格, 用来数据重新回写格式化
     * @return TablePlugin
     */
    public function getTable(): TablePlugin
    {
        return $this->table;
    }


    /**
     * Easy Table 的数据
     * @param array $data
     * @return $this
     */
    public function easy(array $data): self
    {
        $rows = collect($data)->map(function ($row) {
            $newItem = [];
            foreach ($row as $k => $v) {
                $newItem['k' . $k] = $v;
            }
            return $newItem;
        });
        $this->setAttribute('is-easy', true);
        $this->setAttribute('easy-data', $rows);
        return $this;
    }
}
