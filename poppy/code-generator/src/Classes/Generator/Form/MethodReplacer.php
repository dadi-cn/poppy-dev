<?php

declare(strict_types = 1);

namespace Poppy\CodeGenerator\Classes\Generator\Form;

class MethodReplacer
{
    private string $content;

    /**
     * @param string $content
     */
    public function __construct(string $content)
    {
        $this->content = $content;
    }

    public function replace()
    {
        $this->placeholder()
            ->token();

        return $this->content;
    }

    protected function placeholder(): MethodReplacer
    {
        $this->content = preg_replace('/->placeholder\(.*\)/', '', $this->content);

        return $this;
    }

    protected function token()
    {
        $this->content = preg_replace('/->token\(.*\)/', '', $this->content);

        return $this;
    }

}