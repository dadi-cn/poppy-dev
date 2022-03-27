<?php

namespace Poppy\MgrPage\Classes\Grid\Filter\Presenter;

class DateTime extends Presenter
{
    /**
     * @var array
     */
    protected $options = [
        'layui-type' => 'datetime',
    ];

    /**
     * DateTime constructor.
     *
     * @param array $options
     */
    public function __construct(array $options = [])
    {
        $this->options = $this->getOptions($options);
    }


    public function variables(): array
    {
        return [
            'group'   => $this->filter->group,
            'options' => $this->options,
        ];
    }

    /**
     * @param array $options
     *
     * @return mixed
     */
    protected function getOptions(array $options): array
    {
        return array_merge($this->options, $options);
    }
}
