<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;

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
     * @param  \Throwable  $exception
     * @return void
     *
     * @throws \Exception
     */
    public function report(Throwable $exception)
    {
        parent::report($exception);
    }


    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Throwable  $exception
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @throws \Throwable
     */
    public function render($request, Throwable $exception)
    {
        //  if(!env('APP_DEBUG') && $request->is('api/*')){
        //     if($exception->getMessage() == "Unauthenticated."){
        //         return response()->json(
        //             config('exceptions.unauthorized'),
        //             config('exceptions.unauthorized.status_code')
        //         );
        //     }
        //     return $this->handleApiException($request, $exception);
        // }
        return parent::render($request, $exception);;
    }
   

    private function handleApiException($request, Throwable $exception)
    {
        $exception = $this->prepareException($exception);


        if ($exception instanceof \Illuminate\Auth\AuthenticationException) {
            $exception = $this->unauthenticated($request, $exception);
        }

        if ($exception instanceof \Illuminate\Validation\ValidationException) {
            $exception = $this->convertValidationExceptionToResponse($exception, $request);
        }

        return $this->apiResponse($exception);
    }

    private function apiResponse($exception)
    {
        if (method_exists($exception, 'getStatusCode')) {
            $status_code = $exception->getStatusCode();
        } else {
            $status_code = 500;
        }

        $response = [];

        switch ($status_code) {
            case 401:
                $response['message'] = config('exceptions.unauthorized.message');
                break;
            case 403:
                $response['message'] = config('exceptions.forbidden.message');
                break;
            case 404:
                $response['message'] = config('exceptions.not_found.message');
                break;
            case 405:
                $response['message'] =  config('exceptions.method_not_allow.message');
                break;
            case 422:
                $response['message'] = $exception->original['message'];
                $response['errors'] = $exception->original['errors'];
                break;
            default:
                $response['message'] = ($status_code == 500) ? 'Whoops, looks like something went wrong' : $exception->getMessage();
                break;
        }
        if (config('app.debug')) {
            $response['trace'] = $exception->getTrace();
            $response['code'] = $exception->getCode();
        }

        $response['status_code'] = $status_code;
        $response['success'] = false;

        return response()->json($response, $status_code);
    }

    protected function unauthenticated($request, \Illuminate\Auth\AuthenticationException $exception)
    {
        $resp = [];
        $resp['message'] = config('exceptions.unauthorized.message');
        $resp['status_code'] = config('exceptions.unauthorized.status_code');
        return $request->is('api/*')
                    ? response()->json($resp, config('exceptions.unauthorized.status_code'))
                    : redirect()->guest($exception->redirectTo() ?? route('login'));
    }
}
