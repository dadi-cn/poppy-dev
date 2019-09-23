<?php namespace Poppy\Framework\Http\Middlewares;

use Closure;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class EnableCrossRequest.
 */
class EnableCrossRequest
{
	/**
	 * @var ResponseFactory
	 */
	protected $response;

	/**
	 * EnableCrossRequest constructor.
	 * @param ResponseFactory $response response
	 */
	public function __construct(ResponseFactory $response)
	{
		$this->response = $response;
	}

	/**
	 * Middleware handler.
	 * @param Request $request request
	 * @param Closure $next    next
	 * @return mixed
	 */
	public function handle(Request $request, Closure $next)
	{
		$headers  = collect([
			'Access-Control-Allow-Origin'      => '*',
			'Access-Control-Allow-Headers'     => 'Origin,Content-Type,Cookie,Accept,Authorization,X-Requested-With',
			'Access-Control-Allow-Methods'     => 'DELETE,GET,POST,PATCH,PUT,OPTIONS',
			'Access-Control-Allow-Credentials' => 'true',
		]);
		$response = $next($request);
		if ($response instanceof Response) {
			$headers->each(function ($value, $key) use ($response) {
				$response->headers->set($key, $value);
			});
		}
		else {
			$headers->each(function ($value, $key) use ($response) {
				$response->header($key, $value);
			});
		}

		return $response;
	}
}