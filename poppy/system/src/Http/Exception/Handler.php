<?php

namespace Poppy\System\Http\Exception;

use Exception;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\Exceptions\PostTooLargeException;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Session\TokenMismatchException;
use Poppy\Framework\Classes\Resp;
use Poppy\Framework\Foundation\Exception\Handler as ExceptionHandler;

class Handler extends ExceptionHandler
{
    /**
     * All of the register exception handlers.
     * @var array
     */
    protected $handlers = [];

    /**
     * Render an exception into an HTTP response.
     * @param Request   $request
     * @param Exception $exception
     * @return Response
     */
    public function render($request, Exception $exception)
    {
        if ($exception instanceof PostTooLargeException) {
            return Resp::error('请求超过最大限制');
        }

        if ($exception instanceof TokenMismatchException) {
            return Resp::error('CSRF 凭证不匹配');
        }

        return parent::render($request, $exception);
    }

    protected function unauthenticated($request, AuthenticationException $exception)
    {
        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'Unauthorized',
            ], 401);
        }

        return Resp::error('无权限访问');
    }
}
