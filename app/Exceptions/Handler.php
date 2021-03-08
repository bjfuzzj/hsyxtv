<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array
     */
    protected $dontFlash = [
        'password',
        'password_confirmation',
    ];

    /**
     * Report or log an exception.
     *
     * @param \Exception $exception
     * @return void
     */
    public function report(Exception $exception)
    {
        parent::report($exception);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Exception $exception
     * @return \Illuminate\Http\Response
     */
    public function render($request, Exception $exception)
    {
        // 参数验证错误的异常
        if ($exception instanceof ValidationException) {
            Log::error($request->getUri() . ' 参数错误 params:' . print_r($request->all(), 1)
                . ' errors:' . print_r($exception->errors(), 1));
            return response()->json(['code' => 500, 'msg' => Arr::first(Arr::collapse($exception->errors())), 'data' => []]);
        }

        // 用户认证的异常
        if ($exception instanceof UnauthorizedHttpException) {
            return response()->json(['error' => $exception->getMessage()], 401);
        }

        return parent::render($request, $exception);
    }
}
