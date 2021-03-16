<?php namespace Poppy\System\Classes\Grid\Concerns;

use Closure;
use Poppy\System\Classes\Grid;
use Poppy\System\Classes\Grid\Tools\Header;

/**
 * @mixin Grid
 */
trait HasHeader
{
    /**
     * @var Closure
     */
    protected $header;

    /**
     * Set grid header.
     *
     * @param Closure|null $closure
     *
     * @return $this|Closure
     */
    public function header(Closure $closure = null)
    {
        if (!$closure) {
            return $this->header;
        }

        $this->header = $closure;

        return $this;
    }

    /**
     * @return string
     */
    public function renderHeader(): string
    {
        if (!$this->header) {
            return '';
        }

        return (new Header($this))->render();
    }
}
