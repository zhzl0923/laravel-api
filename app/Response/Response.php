<?php

namespace App\Response;

use Facade\Ignition\DumpRecorder\Dump;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Pagination\AbstractPaginator;
use Illuminate\Support\Arr;

class Response
{
    public const HTTP_OK = 200;
    public const HTTP_CREATED = 201;
    public const HTTP_ACCEPTED = 202;
    public const HTTP_NO_CONTENT = 204;
    public const HTTP_BAD_REQUEST = 400;
    public const HTTP_UNAUTHORIZED = 401;
    public const HTTP_FORBIDDEN = 403;
    public const HTTP_NOT_FOUND = 404;
    public const HTTP_METHOD_NOT_ALLOWED = 405;
    public const HTTP_UNPROCESSABLE_ENTITY = 422;
    public const HTTP_INTERNAL_SERVER_ERROR = 500;

    /**
     * Respond with a created response and associate a location if provided.
     *
     * @param null   $data
     * @param string $message
     * @param string $location
     *
     * @return JsonResponse
     */
    public function created($data = null, string $message = '', string $location = '')
    {
        $response = $this->success($data, $message, self::HTTP_CREATED);
        if ($location) {
            $response->header('Location', $location);
        }

        return $response;
    }

    /**
     * Respond with an accepted response and associate a location and/or content if provided.
     *
     * @param null   $data
     * @param string $message
     * @param string $location
     *
     * @return JsonResponse
     */
    public function accepted($data = null, string $message = '', string $location = '')
    {
        $response = $this->success($data, $message, self::HTTP_ACCEPTED);
        if ($location) {
            $response->header('Location', $location);
        }

        return $response;
    }

    /**
     * Respond with a no content response.
     *
     * @param string $message
     *
     * @return JsonResponse
     */
    public function noContent(string $message = '')
    {
        return $this->success(null, $message, self::HTTP_NO_CONTENT);
    }

    /**
     * Return a 401 unauthorized error.
     *
     * @param string $message
     *
     * @return JsonResponse
     */
    public function errorUnauthorized(string $message = '')
    {
        return $this->error($message, self::HTTP_UNAUTHORIZED);
    }

    /**
     * Return a 403 forbidden error.
     *
     * @param string $message
     *
     * @return JsonResponse
     */
    public function errorForbidden(string $message = '')
    {
        return $this->error($message, self::HTTP_FORBIDDEN);
    }

    /**
     * Return a 404 not found error.
     *
     * @param string $message
     *
     * @return JsonResponse
     */
    public function errorNotFound(string $message = '')
    {
        return $this->error($message, self::HTTP_NOT_FOUND);
    }

    /**
     * Return a 405 method not allowed error.
     *
     * @param string $message
     *
     * @return JsonResponse
     */
    public function errorMethodNotAllowed(string $message = '')
    {
        return $this->fail($message, self::HTTP_METHOD_NOT_ALLOWED);
    }

    /**
     * Return a 422 unprocessable entity error.
     *
     * @param string $message
     *
     * @return JsonResponse
     */
    public function errorUnprocessableEntity(string $message = '')
    {
        return $this->fail($message, self::HTTP_UNPROCESSABLE_ENTITY);
    }

    /**
     * Return an fail response.
     *
     * @param  string  $message
     * @param  int  $code
     * @param  array|null  $errors
     * @param  array  $header
     * @param  int  $options
     * @return JsonResponse
     *
     * @throws HttpResponseException
     */
    public function fail(string $message = '', int $code = 400, $errors = null, array $header = [], int $options = 0)
    {
        return $this->error($message, $code, $errors, $header, $options);
    }

    /**
     * Return an fail response.
     *
     * @param  string  $message
     * @param  int  $code
     * @param  array|null  $errors
     * @param  array  $header
     * @param  int  $options
     * @return JsonResponse
     *
     * @throws HttpResponseException
     */
    public function error(string $message = '', int $code = 500, $errors = null, array $header = [], int $options = 0)
    {
        $response = $this->response(
            $this->formatData(null, $message, $code, $errors),
            $code,
            $header,
            $options
        );

        if (is_null($errors)) {
            $response->throwResponse();
        }
        return $response;
    }
    /**
     * Return an success response.
     *
     * @param  JsonResource|array|mixed  $data
     * @param  string  $message
     * @param  int  $code
     * @param  array  $headers
     * @param  int  $option
     * @return JsonResponse|JsonResource
     */
    public function success($data = [], string $message = '', int $code = 200, array $headers = [], int $option = 0)
    {
        if ($data instanceof ResourceCollection) {
            return $this->formatResourceCollectionResponse(...func_get_args());
        }

        if ($data instanceof JsonResource) {
            return $this->formatResourceResponse(...func_get_args());
        }

        if ($data instanceof AbstractPaginator) {
            return $this->formatPaginatedResponse(...func_get_args());
        }

        if ($data instanceof Arrayable) {
            $data = $data->toArray();
        }

        return $this->formatArrayResponse(Arr::wrap($data), $message, $code, $headers, $option);
    }



    /**
     * Format normal array data.
     *
     * @param  array|null  $data
     * @param  string  $message
     * @param  int  $code
     * @param  array  $headers
     * @param  int  $option
     * @return JsonResponse
     */
    protected function formatArrayResponse(array $data, string $message = '', int $code = 200, array $headers = [], int $option = 0): JsonResponse
    {
        return $this->response($this->formatData($data, $message, $code), $code, $headers, $option);
    }

    /**
     * Format return data structure.
     *
     * @param  JsonResource|array|null  $data
     * @param $message
     * @param $code
     * @param  null  $errors
     * @return array
     */
    protected function formatData($data, $message, $code, $errors = null): array
    {
        if ($code >= 400 && $code <= 499) { // client error
            $status = 'fail';
        } elseif ($code >= 500 && $code <= 599) { // service error
            $status = 'error';
        } else {
            $status = 'success';
        }

        if ($code == self::HTTP_UNAUTHORIZED) {
            $status = 'unauthorized';
        }

        if ($code == self::HTTP_UNPROCESSABLE_ENTITY) {
            $status = 'validation';
        }

        $errMessage = config("response.code.{$status}");
        if (false !== strpos($errMessage, '|')) {
            list($errMessage, $businessCode) = explode('|', $errMessage, 2);
        }

        return [
            'status' => $status,
            'code' => (string)($businessCode ?? $code),
            'message' => $message ?: $errMessage,
            'data' => $data ?: (object)$data,
            'errors' => $errors ?: (object)[],
        ];
    }

    /**
     * Format paginated response.
     *
     * @param  AbstractPaginator  $resource
     * @param  string  $message
     * @param  int  $code
     * @param  array  $headers
     * @param  int  $option
     * @return mixed
     */
    protected function formatPaginatedResponse($resource, string $message = '', $code = 200, array $headers = [], $option = 0)
    {
        $paginated = $resource->toArray();

        $paginationInformation = $this->formatPaginatedData($paginated);

        $data = array_merge_recursive(['list' => $paginated['data']], $paginationInformation);

        return $this->response($this->formatData($data, $message, $code), $code, $headers, $option);
    }

    /**
     * Format paginated data.
     *
     * @param  array  $paginated
     * @return array
     */
    protected function formatPaginatedData(array $paginated)
    {
        return [
            'meta' => [
                'pagination' => [
                    'total' => $paginated['total'] ?? 0,
                    'count' => $paginated['to'] ?? 0,
                    'per_page' => $paginated['per_page'] ?? 0,
                    'current_page' => $paginated['current_page'] ?? 0,
                    'total_pages' => $paginated['last_page'] ?? 0
                ],
            ],
        ];
    }

    /**
     * Format collection resource response.
     *
     * @param  JsonResource  $resource
     * @param  string  $message
     * @param  int  $code
     * @param  array  $headers
     * @param  int  $option
     * @return mixed
     */
    protected function formatResourceCollectionResponse($resource, string $message = '', int $code = 200, array $headers = [], int $option = 0)
    {
        $data = array_merge_recursive(['data' => $resource->resolve(request())], $resource->with(request()), $resource->additional);
        if ($resource->resource instanceof AbstractPaginator) {
            $paginated = $resource->resource->toArray();
            $paginationInformation = $this->formatPaginatedData($paginated);

            $data = array_merge_recursive($data, $paginationInformation);
        }

        return tap(
            $this->response($this->formatData($data, $message, $code), $code, $headers, $option),
            function ($response) use ($resource) {
                $response->original = $resource->resource->map(
                    function ($item) {
                        return is_array($item) ? Arr::get($item, 'resource') : $item->resource;
                    }
                );

                $resource->withResponse(request(), $response);
            }
        );
    }

    /**
     * Format JsonResource Data.
     *
     * @param  JsonResource  $resource
     * @param  string  $message
     * @param  int  $code
     * @param  array  $headers
     * @param  int  $option
     * @return mixed
     */
    protected function formatResourceResponse($resource, string $message = '', $code = 200, array $headers = [], $option = 0)
    {
        $resourceData = array_merge_recursive($resource->resolve(request()), $resource->with(request()), $resource->additional);

        return tap(
            $this->response($this->formatData($resourceData, $message, $code), $code, $headers, $option),
            function ($response) use ($resource) {
                $response->original = $resource->resource;

                $resource->withResponse(request(), $response);
            }
        );
    }

    /**
     * Return a new JSON response from the application.
     *
     * @param  mixed  $data
     * @param  int  $status
     * @param  array  $headers
     * @param  int  $options
     * @return JsonResponse
     */
    protected function response($data = [], int $status = 200, array $headers = [], int $options = 0): JsonResponse
    {
        if (false === config('response.is_restful')) {
            $status = self::HTTP_OK;
        }
        return new JsonResponse($data, $status, $headers, $options);
    }
}
