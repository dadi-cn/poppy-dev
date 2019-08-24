<?php namespace Poppy\Framework\Router\Abstracts;

use Closure;
use Exception;
use Illuminate\Container\Container;
use Illuminate\Contracts\Logging\Log;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Validation\ValidationException;
use Poppy\Framework\Router\Responses\ApiResponse;
use Symfony\Component\Translation\TranslatorInterface;
use Throwable;

/**
 * Class Handler.
 */
abstract class Handler
{
	use ValidatesRequests;

	/**
	 * @var int
	 */
	protected $code = 200;

	/**
	 * @var Container
	 */
	protected $container;

	/**
	 * @var Collection
	 */
	protected $data;

	/**
	 * @var array
	 */
	protected $errors;

	/**
	 * @var Collection
	 */
	protected $extra;

	/**
	 * @var Log
	 */
	protected $log;

	/**
	 * @var array
	 */
	protected $messages;

	/**
	 * @var Request
	 */
	protected $request;

	/**
	 * @var TranslatorInterface
	 */
	protected $translator;

	/**
	 * Handler constructor.
	 * @param Container $container
	 */
	public function __construct(Container $container)
	{
		$this->container  = $container;
		$this->data       = new Collection();
		$this->errors     = new Collection();
		$this->extra      = new Collection();
		$this->log        = $this->container->make('log');
		$this->messages   = new Collection();
		$this->request    = $this->container->make('request');
		$this->translator = $this->container->make('translator');
	}

	/**
	 * Begin transaction for database.
	 * @throws Exception
	 */
	protected function beginTransaction()
	{
		$this->container->make('db')->beginTransaction();
	}

	/**
	 * commit transaction for database.
	 */
	protected function commitTransaction()
	{
		$this->container->make('db')->commit();
	}

	/**
	 * Execute Handler.
	 * @throws Exception
	 */
	abstract protected function execute();

	/**
	 * @param ApiResponse $response
	 * @param Exception   $exception
	 * @return ApiResponse
	 */
	protected function handleExceptions(ApiResponse $response, Exception $exception)
	{
		if ($exception instanceof ValidationException) {
			return $response->withParams([
				'code'    => 422,
				'message' => $exception->validator->errors()->getMessages(),
				'trace'   => $exception->getTrace(),
			]);
		}

		return $response->withParams([
			'code'    => $exception->getCode(),
			'message' => $exception->getMessage(),
			'trace'   => $exception->getTrace(),
		]);
	}

	/**
	 * Rollback transaction for database.
	 */
	protected function rollBackTransaction()
	{
		$this->container->make('db')->rollBack();
	}

	/**
	 * @param Closure $closure
	 * @param int     $attempts
	 * @throws Exception
	 * @throws Throwable
	 */
	protected function transaction(Closure $closure, $attempts = 1)
	{
		$this->container->make('db')->transaction($closure, $attempts);
	}

	/**
	 * Make data to response with errors or messages.
	 * @return ApiResponse
	 * @throws Exception
	 */
	public function toResponse()
	{
		$response = new ApiResponse();
		try {
			$this->execute();
			if ($this->code !== 200) {
				$messages = $this->errors->toArray();
			}
			else {
				$messages = $this->messages->toArray();
			}
			$response = $response->withParams([
				'code'    => $this->code,
				'data'    => $this->data->toArray(),
				'message' => $messages,
			]);
			if ($this->extra->count()) {
				$response = $response->withParams($this->extra->toArray());
			}

			return $response;
		} catch (Exception $exception) {
			return $this->handleExceptions($response, $exception);
		}
	}

	/**
	 * @param int $code
	 * @return $this
	 */
	protected function withCode($code)
	{
		$this->code = $code;

		return $this;
	}

	/**
	 * @param      $data
	 * @param bool $override
	 * @return $this
	 */
	protected function withData($data, $override = false)
	{
		if ($data instanceof Arrayable) {
			$data = $data->toArray();
		}
		foreach ((array) $data as $key => $value) {
			if (is_numeric($key)) {
				if ($this->data->has($key) && $override) {
					$this->data->push($value);
				}
				else {
					$this->data->put($key, $value);
				}
			}
			else {
				$this->data->put($key, $value);
			}
		}

		return $this;
	}

	/**
	 * @param array|string $errors
	 * @return $this
	 */
	protected function withError($errors)
	{
		foreach ((array) $errors as $error) {
			$this->errors->push($this->translator->trans($error));
		}

		return $this;
	}

	/**
	 * @param $extras
	 * @return $this
	 */
	public function withExtra($extras)
	{
		foreach ((array) $extras as $key => $extra) {
			if (!is_numeric($key)) {
				$this->extra->put($key, $extra);
			}
		}

		return $this;
	}

	/**
	 * @param array|string $messages
	 * @return $this
	 */
	protected function withMessage($messages)
	{
		foreach ((array) $messages as $message) {
			$this->messages->push($this->translator->trans($message));
		}

		return $this;
	}
}
