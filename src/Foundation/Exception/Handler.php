<?php namespace Poppy\Framework\Foundation\Exception;

use Closure;
use Event;
use Exception;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Poppy\Framework\Exceptions\AjaxException;
use ReflectionFunction;
use Response;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;

/**
 * poppy handler
 */
class Handler extends ExceptionHandler
{
	/**
	 * A list of the exception types that should not be reported.
	 * @var array
	 */
	protected $dontReport = [
		'Illuminate\Database\Eloquent\ModelNotFoundException',
		'Symfony\Component\HttpKernel\Exception\HttpException',
	];

	/**
	 * All of the register exception handlers.
	 * @var array
	 */
	protected $handlers = [];

	/**
	 * Render an exception into an HTTP response.
	 * @param Request   $request   request
	 * @param Exception $exception exception
	 * @return \Illuminate\Http\Response
	 * @throws \ReflectionException
	 */
	public function render($request, Exception $exception)
	{
		if (!class_exists('Event')) {
			return parent::render($request, $exception);
		}

		$statusCode = $this->getStatusCode($exception);

		$response = $this->callCustomHandlers($exception);

		if (!is_null($response)) {
			return Response::make($response, $statusCode);
		}

		if ($event = Event::dispatch('exception.beforeRender', [$exception, $statusCode, $request], true)) {
			return Response::make($event, $statusCode);
		}

		return parent::render($request, $exception);
	}

	/**
	 * Checks if the exception implements the HttpExceptionInterface, or returns
	 * as generic 500 error code for a server side error.
	 * @param Exception $exception exception
	 * @return int
	 */
	protected function getStatusCode($exception)
	{
		if ($exception instanceof HttpExceptionInterface) {
			$code = $exception->getStatusCode();
		}
		elseif ($exception instanceof AjaxException) {
			$code = 406;
		}
		elseif ($exception instanceof ValidationException) {
			$code = 403;
		}
		else {
			$code = 500;
		}

		return $code;
	}

	/**
	 * Get the default context variables for logging.
	 * @return array
	 */
	protected function context()
	{
		return [];
	}

	//
	// Custom handlers
	//

	/**
	 * Register an application error handler.
	 * @param Closure $callback callback
	 * @return void
	 */
	public function error(Closure $callback)
	{
		array_unshift($this->handlers, $callback);
	}

	/**
	 * Handle the given exception.
	 * @param Exception $exception   exception
	 * @param bool      $fromConsole from console
	 * @return void
	 * @throws \ReflectionException
	 */
	protected function callCustomHandlers($exception, $fromConsole = false)
	{
		foreach ($this->handlers as $handler) {
			// If this exception handler does not handle the given exception, we will just
			// go the next one. A handler may type-hint an exception that it handles so
			//  we can have more granularity on the error handling for the developer.
			if (!$this->handlesException($handler, $exception)) {
				continue;
			}

			$code = $this->getStatusCode($exception);

			// We will wrap this handler in a try / catch and avoid white screens of death
			// if any exceptions are thrown from a handler itself. This way we will get
			// at least some errors, and avoid errors with no data or not log writes.
			try {
				$response = $handler($exception, $code, $fromConsole);
			} catch (Exception $e) {
				$response = $this->formatException($e);
			}
			// If this handler returns a "non-null" response, we will return it so it will
			// get sent back to the browsers. Once the handler returns a valid response
			// we will cease iterating through them and calling these other handlers.
			if (isset($response) && !is_null($response)) {
				return $response;
			}
		}
	}

	/**
	 * Determine if the given handler handles this exception.
	 * @param Closure   $handler   handler
	 * @param Exception $exception exception
	 * @return bool
	 * @throws \ReflectionException
	 */
	protected function handlesException(Closure $handler, $exception)
	{
		$reflection = new ReflectionFunction($handler);

		return $reflection->getNumberOfParameters() == 0 || $this->hints($reflection, $exception);
	}

	/**
	 * Determine if the given handler type hints the exception.
	 * @param ReflectionFunction $reflection reflection
	 * @param Exception          $exception  exception
	 * @return bool
	 */
	protected function hints(ReflectionFunction $reflection, $exception)
	{
		$parameters = $reflection->getParameters();
		$expected   = $parameters[0];

		return !$expected->getClass() || $expected->getClass()->isInstance($exception);
	}
}
