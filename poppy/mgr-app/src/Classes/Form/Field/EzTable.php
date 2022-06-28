<?php

namespace Poppy\MgrApp\Classes\Form\Field;

use Poppy\MgrApp\Classes\Form\FormItem;

class EzTable extends FormItem
{

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
        $this->setAttribute('data', $rows);
        return $this;
    }
}
