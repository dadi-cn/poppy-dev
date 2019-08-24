<?php namespace Poppy\Framework\Router;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Router as RouterBase;

class CoreRouter extends RouterBase
{
	/**
	 * Dispatch the request to the application.
	 *
	 * @param Request $request
	 * @return Response|JsonResponse
	 */
	public function dispatch(Request $request)
	{
		$this->currentRequest = $request;

		$this->events->fire('router.before', [$request]);

		$response = $this->dispatchToRoute($request);

		$this->events->fire('router.after', [$request, $response]);

		return $response;
	}

	/**
	 * Register a new "before" filter with the router.
	 *
	 * @param  string|callable  $callback
	 * @return void
	 */
	public function before($callback)
	{
		$this->events->listen('router.before', $callback);
	}

	/**
	 * Register a new "after" filter with the router.
	 *
	 * @param  string|callable  $callback
	 * @return void
	 */
	public function after($callback)
	{
		$this->events->listen('router.after', $callback);
	}
}
