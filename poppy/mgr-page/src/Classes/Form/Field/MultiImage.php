<?php

namespace Poppy\MgrPage\Classes\Form\Field;

use Poppy\MgrPage\Classes\Form\Field;

final class MultiImage extends Field
{

    /**
     * @inheritDoc
     */
    protected $view = 'py-mgr-page::tpl.form.multi_image';

    /**
     * Token
     * @var string
     */
    private $token;

    /**
     * 上传数量
     * @var int
     */
    private $number;


    /**
     * @var bool 自动上传
     */
    private $auto = false;

    public function token($token): self
    {
        $this->token = $token;
        return $this;
    }

    /**
     * 最大上传数量
     * @param $number
     */
    public function number($number)
    {
        $this->number = $number;
    }

    public function auto($auto = false)
    {
        $this->auto = $auto;
    }


    public function render()
    {
        $this->attribute([
            'token'  => $this->token,
            'number' => $this->number,
            'auto'   => $this->auto,
        ]);
        return parent::render();
    }
}
