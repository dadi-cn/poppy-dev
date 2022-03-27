<?php

namespace Poppy\MgrPage\Classes\Actions;

use BadMethodCallException;
use Exception;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use function e;
use function Poppy\System\Classes\Actions\admin_url;
use function Poppy\System\Classes\Actions\spl_object_id;

/**
 * @method    success($title, $text = '', $options = [])
 * @method    error($title, $text = '', $options = [])
 * @method    warning($title, $text = '', $options = [])
 * @method    info($title, $text = '', $options = [])
 * @method    question($title, $text = '', $options = [])
 * @method    confirm($title, $text = '', $options = [])
 * @method    modalLarge()
 * @method    modalSmall()
 */
abstract class Action implements Renderable
{

    /**
     * @var string
     */
    public $event = 'click';
    /**
     * @var string
     */
    public $selectorPrefix = '.action-';
    /**
     * @var string
     */
    public $name;
    /**
     * @var Response
     */
    protected $response;
    /**
     * @var string
     */
    protected $selector;
    /**
     * @var string
     */
    protected $method = 'POST';
    /**
     * @var array
     */
    protected $attributes = [];
    /**
     * @var \Poppy\MgrPage\Classes\Actions\Interactor\Interactor
     */
    protected $interactor;
    /**
     * @var array
     */
    protected static $selectors = [];

    /**
     * Action constructor.
     */
    public function __construct()
    {
        $this->initInteractor();
    }

    /**
     * @return mixed
     */
    public function render()
    {

        $content = $this->html();

        if ($content && $this->interactor instanceof \Poppy\MgrPage\Classes\Actions\Interactor\Form) {
            return $this->interactor->addElementAttr($content, $this->selector);
        }

        return $this->html();
    }

    /**
     * @throws Exception
     */
    protected function initInteractor()
    {
        if ($hasForm = method_exists($this, 'form')) {
            $this->interactor = new \Poppy\MgrPage\Classes\Actions\Interactor\Form($this);
        }

        if ($hasDialog = method_exists($this, 'dialog')) {
            $this->interactor = new \Poppy\MgrPage\Classes\Actions\Interactor\Dialog($this);
        }

        if ($hasForm && $hasDialog) {
            throw new Exception('Can only define one of the methods in `form` and `dialog`');
        }
    }

    /**
     * Get batch action title.
     *
     * @return string
     */
    public function name()
    {
        return $this->name;
    }

    /**
     * @param string $prefix
     *
     * @return mixed|string
     */
    public function selector($prefix)
    {
        if (is_null($this->selector)) {
            return static::makeSelector(get_called_class() . spl_object_id($this), $prefix);
        }

        return $this->selector;
    }

    /**
     * @param string $class
     * @param string $prefix
     *
     * @return string
     */
    public static function makeSelector($class, $prefix)
    {
        if (!isset(static::$selectors[$class])) {
            static::$selectors[$class] = uniqid($prefix) . mt_rand(1000, 9999);
        }

        return static::$selectors[$class];
    }

    /**
     * @param string $name
     * @param string $value
     *
     * @return $this
     */
    public function attribute($name, $value)
    {
        $this->attributes[$name] = $value;

        return $this;
    }

    /**
     * Format the field attributes.
     *
     * @return string
     */
    protected function formatAttributes()
    {
        $html = [];

        foreach ($this->attributes as $name => $value) {
            $html[] = $name . '="' . e($value) . '"';
        }

        return implode(' ', $html);
    }

    /**
     * @return string
     */
    protected function getElementClass()
    {
        return ltrim($this->selector($this->selectorPrefix), '.');
    }

    /**
     * @return Response
     */
    public function response()
    {
        if (is_null($this->response)) {
            $this->response = new Response();
        }

        if (method_exists($this, 'dialog')) {
            $this->response->swal();
        }
        else {
            $this->response->toastr();
        }

        return $this->response;
    }

    /**
     * @return string
     */
    public function getMethod()
    {
        return $this->method;
    }

    /**
     * @return mixed
     */
    public function getCalledClass()
    {
        return str_replace('\\', '_', get_called_class());
    }

    /**
     * @return string
     */
    public function getHandleRoute()
    {
        return admin_url('_handle_action_');
    }

    /**
     * @return string
     */
    protected function getModelClass()
    {
        return '';
    }

    /**
     * @return array
     */
    public function parameters()
    {
        return [];
    }

    /**
     * @param Request $request
     *
     * @return $this
     */
    public function validate(Request $request)
    {
        if ($this->interactor instanceof \Poppy\MgrPage\Classes\Actions\Interactor\Form) {
            $this->interactor->validate($request);
        }

        return $this;
    }

    /**
     * @param string $method
     * @param array  $arguments
     *
     * @return mixed
     * @throws Exception
     *
     */
    public function __call($method, $arguments = [])
    {
        if (in_array($method, \Poppy\MgrPage\Classes\Actions\Interactor\Interactor::$elements)) {
            return $this->interactor->{$method}(...$arguments);
        }

        throw new BadMethodCallException("Method {$method} does not exist.");
    }

    /**
     * @return string
     */
    public function html()
    {
    }
}
