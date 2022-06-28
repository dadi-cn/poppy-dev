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
     * 不起作用, 使用 ezTable
     * @param array $data
     * @return $this
     * @deprecated  4.0-dev
     */
    public function easy(array $data): self
    {
        return $this;
    }
}
