<?php

namespace Poppy\MgrApp\Classes\Form\Field;

use Poppy\MgrApp\Classes\Form\FormItem;
use Poppy\MgrApp\Classes\Form\Traits\UsePlaceholder;
use Poppy\MgrApp\Classes\Form\Traits\UseText;

class Textarea extends FormItem
{

    use UseText, UsePlaceholder;

    /**
     * Default rows of textarea.
     * @var int
     */
    protected int $rows = 5;

    public function __construct(string $name, string $label)
    {
        parent::__construct($name, $label);
        $this->rows($this->rows);
    }

    /**
     * 控制是否用户可以缩放
     * @param $type
     * @return Textarea
     */
    public function resize($type): self
    {
        if (!in_array($type, ['none', 'both', 'horizontal', 'vertical'])) {
            $type = 'none';
        }
        $this->setAttribute('resize', $type);
        return $this;
    }

    /**
     * @param bool|array $args
     * @return $this
     */
    public function autosize(...$args): self
    {
        if (count($args) === 0) {
            $this->setAttribute('autosize', true);
        }
        if (count($args) === 1) {
            if (is_bool($args[0])) {
                $this->setAttribute('autosize', $args[0]);
            } elseif (is_array($args[0])) {
                $this->setAttribute('autosize', $args[0]);
            } elseif (is_numeric($args[0])) {
                $this->setAttribute('autosize', [
                    'minRows' => ((int) $args[0]) ?: 1,
                ]);
            }
        }

        if (count($args) === 2) {
            $this->setAttribute('autosize', [
                'minRows' => ((int) $args[0]) ?: 1,
                'maxRows' => ((int) $args[1]) ?: 4,
            ]);
        }
        return $this;
    }

    public function rows($rows = 5): self
    {
        $this->setAttribute('rows', $rows);
        return $this;
    }
}
