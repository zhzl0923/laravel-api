<?php

namespace App\Traits;

use Throwable;
use App\Response\Facade\Response;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\{Request, JsonResponse};
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response as SymfonyResponse;

trait ExceptionTrait
{

    /**
     * Convert an authentication exception into a response.
     *
     * @param Request                 $request
     * @param AuthenticationException $exception
     *
     * @return SymfonyResponse
     */
    protected function unauthenticated($request, AuthenticationException $exception)
    {
        return $request->expectsJson()
            ? Response::errorUnauthorized($exception->getMessage())
            : redirect()->guest($exception->redirectTo() ?? route('login'));
    }

    /**
     * Prepare a JSON response for the given exception.
     *
     * @param Request   $request
     * @param Throwable $e
     *
     * @return JsonResponse
     */
    protected function prepareJsonResponse($request, Throwable $e)
    {

        return Response::error(
            $e->getMessage(),
            $this->isHttpException($e) ? $e->getStatusCode() : 500,
            $this->convertExceptionToArray($e),
            $this->isHttpException($e) ? $e->getHeaders() : [],
            JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES
        );
    }

    /**
     * Prepare a response for the given exception.
     *
     * @param Request   $request
     * @param Throwable $e
     *
     * @return SymfonyResponse|JsonResponse
     */
    protected function prepareResponse($request, Throwable $e)
    {

        if (config('response.is_unified_return_json')) {
            return $this->prepareJsonResponse($request, $e);
        }

        return parent::prepareResponse($request, $e);
    }

    /**
     * Convert a validation exception into a JSON response.
     *
     * @param Request             $request
     * @param ValidationException $exception
     *
     * @return JsonResponse
     */
    protected function invalidJson($request, ValidationException $exception)
    {
        return Response::fail(
            $exception->getMessage(),
            $exception->status,
            $exception->errors()
        );
    }

    /**
     * Custom Failed Validation Response for Lumen.
     *
     * @param  Request  $request
     * @param  array  $errors
     * @return mixed
     *
     * @throws HttpResponseException
     */
    protected function buildFailedValidationResponse(Request $request, array $errors)
    {
        if (isset(static::$responseBuilder)) {
            return (static::$responseBuilder)($request, $errors);
        }

        $firstMessage = Arr::first($errors, null, '');
        return Response::fail(
            is_array($firstMessage) ? Arr::first($firstMessage) : $firstMessage,
            422,
            $errors
        );
    }
}
