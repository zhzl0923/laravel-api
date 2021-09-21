<?php

namespace App\Response\Facade;

use Illuminate\Support\Facades\Facade as LaravelFacade;
use Illuminate\Http\JsonResponse;

/**
 * @method static JsonResponse success($data = null, string $message = '', $code = 200, array $headers = [], $option = 0)
 * @method static JsonResponse fail(string $message = '', $code = 400, $errors = null, array $headers = [], $option = 0)
 * @method static JsonResponse error(string $message = '', $code = 500, $errors = null, array $headers = [], $option = 0)
 * @method static JsonResponse accepted($data = null, string $message = '', string $location = '')
 * @method static JsonResponse created($data = null, string $message = '', string $location = '')
 * @method static JsonResponse noContent(string $message = '')
 * @method static JsonResponse errorUnauthorized(string $message = '')
 * @method static JsonResponse errorForbidden(string $message = '')
 * @method static JsonResponse errorNotFound(string $message = '')
 * @method static JsonResponse errorMethodNotAllowed(string $message = '')
 * @method static JsonResponse errorUnprocessableEntity(string $message = '')
 *
 * @see \App\Response\Response
 */
class Response extends LaravelFacade
{
    /**
     * {@inheritDoc}
     */
    protected static function getFacadeAccessor()
    {
        return \App\Response\Response::class;
    }
}
