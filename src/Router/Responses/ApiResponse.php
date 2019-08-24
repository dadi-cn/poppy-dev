<?php namespace Poppy\Framework\Router\Responses;

use Illuminate\Http\Response;
use Psr\Http\Message\ResponseInterface;

/**
 * Class ApiResponse.
 */
class ApiResponse
{
	/**
	 * @var array
	 */
	protected $params = [];

	/**
	 * Generate a api response to http response.
	 * @param ResponseInterface|null $response
	 * @param array                  $params
	 * @return ApiResponse|ResponseInterface
	 */
	public function generateHttpResponse(ResponseInterface $response = null, $params = [])
	{
		is_null($response) && $response = new Response();
		$params && $this->params        = array_merge($this->params, $params);
		$status                         = collect($this->params)->get('code', 200);
		if (!is_int($status)) {
			$status = 500;
		}
		if ($status > 598 || $status < 100) {
			$status = 500;
		}
		$response = $response->withStatus($status)
			->withHeader('pragma', 'no-cache')
			->withHeader('cache-control', 'no-store')
			->withHeader('content-type', 'application/json; charset=UTF-8');
		$response->getBody()->write(json_encode($this->params));

		return $response;
	}

	/**
	 * Add params to api response.
	 * @param array $params
	 * @return $this
	 */
	public function withParams($params = [])
	{
		$this->params = array_merge($this->params, $params);

		return $this;
	}
}
