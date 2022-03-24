<?php


namespace Poppy\MgrApp\Classes\Form\Traits;

use Illuminate\Support\Arr;

trait UsePlaceholder
{

    /**
     * @param array|string $ph 占位符
     * @return $this
     */
    public function placeholder($ph): self
    {
        if (is_array($ph)) {
            $first = Arr::first($ph);
            $last  = Arr::last($ph);
            $this->setAttribute('start_placeholder', $first);
            $this->setAttribute('end_placeholder', $last);
        } else {
            $this->setAttribute('placeholder', $ph);
        }
        return $this;
    }
}
