<?php

namespace Poppy\MgrPage\Classes\Form\Field;

use Poppy\MgrPage\Classes\Form\Field;

class Image extends Field
{

    /**
     * @var string Token For Upload
     */
    protected $token;

    /**
     * @var string
     */
    protected $sizeClass = 'form_thumb-normal';


    public function token(string $token)
    {
        $this->token = $token;
        return $this;
    }

    /**
     * 设置不同的图片大小
     * @param string $size [small:60|large:120|normal:85]
     * @return $this
     */
    public function size(string $size = 'normal')
    {
        $sizeClass = 'form_thumb-normal';
        if ($size === 'normal') {
            $sizeClass = 'form_thumb-normal'; // 85
        }
        if ($size === 'small') {
            $sizeClass = 'form_thumb-small'; // 60
        }
        if ($size === 'large') {
            $sizeClass = 'form_thumb-large'; // 120
        }
        $this->sizeClass = $sizeClass;
        return $this;
    }


    public function render()
    {
        $this->attribute([
            'token'     => $this->token,
            'sizeClass' => $this->sizeClass,
        ]);
        return parent::render();
    }
}
