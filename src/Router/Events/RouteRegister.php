<?php namespace Poppy\Framework\Router\Events;

use Illuminate\Routing\Router;

/**
 * Class RouteRegister.
 */
class RouteRegister
{
	/**
	 * @var Router
	 */
	protected $router;

	/**
	 * RouteRegister constructor.
	 * @param Router $router
	 * @internal param \Illuminate\Container\Container|\Illuminate\Contracts\Foundation\Application $container
	 */
	public function __construct(Router $router)
	{
		$this->router = $router;
	}
}
